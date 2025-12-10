<?php

namespace App\Services;

use App\Models\AiChatSession;
use App\Models\AiInsight;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ClaraAiService
{
    private $apiKey;

    private $baseUrl = 'https://openrouter.ai/api/v1';

    public function __construct()
    {
        $this->apiKey = config('services.clara.key');
    }

    public function chat(AiChatSession $session, string $userMessage): array
    {
        $user = $session->user;

        // Check dan reset quota harian
        if (! $this->checkAndResetQuota($user)) {
            return [
                'success' => false,
                'message' => 'Kuota chat harian Anda sudah habis. Kuota akan direset besok.',
            ];
        }

        // Save user message
        $session->addMessage('user', $userMessage);

        // Get business context dengan validasi data
        $contextData = $this->getBusinessContext($session->outlet_id);

        // Build conversation history
        $messages = $this->buildMessages($session, $contextData, $userMessage);

        try {
            \Log::info('Sending request to Clara AI', [
                'messages_count' => count($messages),
                'user_message' => $userMessage,
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->baseUrl.'/chat/completions', [
                'model' => 'amazon/nova-2-lite-v1:free',
                'messages' => $messages,
                'max_tokens' => 2000,
            ]);

            \Log::info('Clara AI Response Status', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $data = $response->json();

                if (! isset($data['choices'][0]['message']['content'])) {
                    \Log::error('Invalid response structure', ['data' => $data]);

                    return [
                        'success' => false,
                        'message' => 'Format response tidak valid.',
                    ];
                }

                $aiResponse = $data['choices'][0]['message']['content'];

                // ✅ BERSIHKAN RESPONSE dari markdown dan whitespace
                $cleanResponse = $this->cleanAiResponse($aiResponse);

                // Save AI response (yang sudah dibersihkan)
                $session->addMessage('assistant', $cleanResponse);

                // Generate insight jika diperlukan
                $this->generateInsightIfNeeded($session->outlet_id, $userMessage, $cleanResponse, $contextData);

                // Kurangi quota
                $user->decrement('daily_chat_quota');

                return [
                    'success' => true,
                    'message' => $cleanResponse, // Kirim yang sudah bersih
                    'remaining_quota' => $user->daily_chat_quota,
                ];
            }

            \Log::error('Clara AI request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Maaf, terjadi kesalahan saat menghubungi Clara AI. Status: '.$response->status(),
            ];
        } catch (\Exception $e) {
            \Log::error('Clara AI Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Error: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Bersihkan response AI dari markdown dan whitespace berlebih
     */
    private function cleanAiResponse(string $response): string
    {
        // 1. Trim whitespace di awal dan akhir
        $response = trim($response);

        // 2. Hapus semua markdown bold (**text** atau __text__)
        $response = preg_replace('/\*\*(.*?)\*\*/', '$1', $response);
        $response = preg_replace('/__(.*?)__/', '$1', $response);

        // 3. Hapus markdown italic (*text* atau _text_) - hati-hati dengan angka
        $response = preg_replace('/(?<!\w)\*([^\*]+?)\*(?!\w)/', '$1', $response);
        $response = preg_replace('/(?<!\w)_([^_]+?)_(?!\w)/', '$1', $response);

        // 4. Hapus markdown headers (# ## ###)
        $response = preg_replace('/^#{1,6}\s+/m', '', $response);

        // 5. Hapus markdown code blocks (```code```)
        $response = preg_replace('/```[\s\S]*?```/', '', $response);
        $response = preg_replace('/`([^`]+)`/', '$1', $response);

        // 6. Normalize multiple spaces menjadi single space
        $response = preg_replace('/[ \t]+/', ' ', $response);

        // 7. Normalize multiple newlines (max 2 newlines)
        $response = preg_replace('/\n{3,}/', "\n\n", $response);

        // 8. Trim setiap baris
        $lines = explode("\n", $response);
        $lines = array_map('trim', $lines);
        $response = implode("\n", $lines);

        // 9. Final trim
        return trim($response);
    }

    private function checkAndResetQuota(User $user): bool
    {
        $today = now()->toDateString();

        if ($user->last_chat_reset_date !== $today) {
            $user->update([
                'daily_chat_quota' => 3,
                'last_chat_reset_date' => $today,
            ]);
        }

        return $user->daily_chat_quota > 0;
    }

    private function getBusinessContext($outletId): array
    {
        // Ambil informasi outlet
        $outlet = DB::table('outlets')
            ->where('id', $outletId)
            ->first();

        // Dapatkan tanggal data paling lama yang tersedia
        $oldestSaleDate = DB::table('sales')
            ->where('outlet_id', $outletId)
            ->where('status', 'completed')
            ->min('created_at');

        $dataAvailableSince = $oldestSaleDate ? Carbon::parse($oldestSaleDate)->format('d M Y') : null;

        // Sales summary - 7 hari terakhir dengan STRICT outlet filter
        $salesSummary = DB::table('sales')
            ->where('outlet_id', $outletId)
            ->where('status', 'completed')
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('
            COUNT(*) as total_transactions,
            SUM(grand_total) as total_revenue,
            AVG(grand_total) as avg_transaction,
            DATE(created_at) as sale_date
        ')
            ->groupBy('sale_date')
            ->orderBy('sale_date', 'desc')
            ->get();

        // Top products dengan STRICT outlet filter
        $topProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->where('sales.outlet_id', $outletId)
            ->where('sales.status', 'completed')
            ->where('sales.created_at', '>=', now()->subDays(7))
            ->selectRaw('
            products.name,
            SUM(sale_items.quantity) as total_sold,
            SUM(sale_items.subtotal) as total_revenue
        ')
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_sold', 'desc')
            ->limit(5)
            ->get();

        // Low stock products dengan STRICT outlet filter
        $lowStock = DB::table('products')
            ->join('product_stocks', 'products.id', '=', 'product_stocks.product_id')
            ->where('product_stocks.outlet_id', $outletId)
            ->whereRaw('product_stocks.quantity <= products.min_stock')
            ->select('products.name', 'product_stocks.quantity', 'products.min_stock')
            ->limit(5)
            ->get();

        // Total revenue hari ini
        $todayRevenue = DB::table('sales')
            ->where('outlet_id', $outletId)
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->sum('grand_total');

        return [
            'outlet_name' => $outlet->name ?? 'Outlet',
            'data_available_since' => $dataAvailableSince,
            'oldest_date' => $oldestSaleDate,
            'sales_summary' => $salesSummary,
            'top_products' => $topProducts,
            'low_stock' => $lowStock,
            'today_revenue' => $todayRevenue,
            'outlet_id' => $outletId,
        ];
    }

    private function buildMessages(AiChatSession $session, array $contextData, string $userMessage): array
    {
        $dataInfo = $contextData['data_available_since']
            ? "Data tersedia sejak: {$contextData['data_available_since']}"
            : 'Belum ada data penjualan';

        $systemPrompt = "Kamu adalah Clara AI, asisten bisnis untuk {$contextData['outlet_name']} yang menggunakan CuanFlow POS.

GAYA KOMUNIKASI:
- Gunakan Bahasa Indonesia yang natural dan ramah
- JANGAN PERNAH gunakan markdown formatting (bold, italic, headers, code blocks)
- JANGAN gunakan simbol: ** __ * _ # ` [ ] 
- JANGAN sebutkan outlet_id atau kode teknis
- Gunakan nama outlet '{$contextData['outlet_name']}' saat merujuk ke bisnis
- Jawab langsung dan to the point tanpa formatting apapun
- Gunakan format angka yang mudah dibaca: Rp 437.000
- Tulis dalam kalimat biasa, tanpa bold atau emphasis apapun

ATURAN DATA:
- Semua data yang kamu berikan untuk {$contextData['outlet_name']}
- {$dataInfo}
- Jika user menanyakan data di luar rentang yang tersedia, jawab: \"Maaf, data untuk periode tersebut belum tersedia. Data penjualan baru ada mulai {$contextData['data_available_since']}\"
- JANGAN membuat asumsi atau prediksi untuk data yang tidak ada
- Jika tidak ada data, katakan dengan jelas

DATA BISNIS SAAT INI:
Pendapatan hari ini: Rp ".number_format($contextData['today_revenue'], 0, ',', '.').'

Ringkasan penjualan 7 hari terakhir:
'.$this->formatSalesSummary($contextData['sales_summary']).'

5 Produk terlaris minggu ini:
'.$this->formatTopProducts($contextData['top_products']).'

Produk dengan stok menipis:
'.$this->formatLowStock($contextData['low_stock']).'

CONTOH RESPONS YANG BENAR:
User: "Berapa pendapatan hari ini?"
Clara: "Pendapatan hari ini mencapai Rp 437.000. Cukup bagus untuk hari Kamis!"

User: "Produk apa yang paling laku?"
Clara: "Minggu ini Takoyaki Pedas jadi juara dengan 6.000 pcs terjual, disusul Takoyaki Keju 4.000 pcs. Pelanggan sepertinya suka varian pedas dan keju!"

PENTING: Tulis semua response dalam text biasa tanpa formatting markdown sama sekali.

Berikan insight yang actionable, singkat, dan mudah dipahami.';

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        // Ambil history dalam urutan yang benar
        $history = $session->messages()
            ->orderBy('created_at', 'asc')
            ->get();

        $lastRole = 'system';
        foreach ($history as $msg) {
            if ($msg->role === 'system') {
                continue;
            }

            if ($msg->role === 'user' && $lastRole !== 'user') {
                $messages[] = ['role' => 'user', 'content' => $msg->content];
                $lastRole = 'user';
            } elseif ($msg->role === 'assistant' && $lastRole === 'user') {
                $messages[] = ['role' => 'assistant', 'content' => $msg->content];
                $lastRole = 'assistant';
            }
        }

        return $messages;
    }

    private function generateInsightIfNeeded($outletId, $userMessage, $aiResponse, $contextData): void
    {
        // Keywords yang memicu pembuatan insight
        $insightTriggers = [
            'analisis' => 'general',
            'insight' => 'general',
            'rekomendasi' => 'product_recommendation',
            'prediksi' => 'stock_prediction',
            'trend' => 'sales_trend',
            'anomali' => 'anomaly',
            'penjualan menurun' => 'anomaly',
            'stok menipis' => 'stock_prediction',
        ];

        $insightType = null;
        foreach ($insightTriggers as $keyword => $type) {
            if (stripos($userMessage, $keyword) !== false) {
                $insightType = $type;
                break;
            }
        }

        // Jika tidak ada trigger keyword, cek apakah ada hal penting dalam response
        if (! $insightType) {
            if (stripos($aiResponse, 'penting') !== false ||
                stripos($aiResponse, 'perhatian') !== false ||
                stripos($aiResponse, 'segera') !== false) {
                $insightType = 'general';
            }
        }

        if (! $insightType) {
            return; // Tidak perlu generate insight
        }

        // Tentukan severity
        $severity = 'info';
        if (stripos($aiResponse, 'segera') !== false ||
            stripos($aiResponse, 'kritis') !== false ||
            stripos($aiResponse, 'bahaya') !== false) {
            $severity = 'critical';
        } elseif (stripos($aiResponse, 'perhatian') !== false ||
                  stripos($aiResponse, 'warning') !== false) {
            $severity = 'warning';
        }

        // Generate title berdasarkan context
        $title = $this->generateInsightTitle($insightType, $contextData);

        // Simpan insight
        AiInsight::create([
            'outlet_id' => $outletId,
            'type' => $insightType,
            'title' => $title,
            'content' => $aiResponse,
            'data' => [
                'user_question' => $userMessage,
                'context_summary' => [
                    'today_revenue' => $contextData['today_revenue'],
                    'low_stock_count' => $contextData['low_stock']->count(),
                    'top_product' => $contextData['top_products']->first()->name ?? null,
                ],
            ],
            'severity' => $severity,
            'insight_date' => now(),
        ]);

        \Log::info('AI Insight generated', [
            'outlet_id' => $outletId,
            'type' => $insightType,
            'severity' => $severity,
        ]);
    }

    private function generateInsightTitle($type, $contextData): string
    {
        $titles = [
            'sales_trend' => 'Analisis Tren Penjualan',
            'stock_prediction' => 'Prediksi Kebutuhan Stok',
            'product_recommendation' => 'Rekomendasi Produk',
            'anomaly' => 'Deteksi Anomali Penjualan',
            'general' => 'Insight Bisnis',
        ];

        $baseTitle = $titles[$type] ?? 'Insight Clara AI';

        // Tambahkan konteks jika ada
        if ($contextData['low_stock']->count() > 0 && $type === 'stock_prediction') {
            return $baseTitle.' - '.$contextData['low_stock']->count().' Produk Stok Menipis';
        }

        return $baseTitle.' - '.now()->format('d M Y');
    }

    /**
     * Generate daily automatic insights
     */
    public function generateDailyInsights($outletId): void
    {
        $contextData = $this->getBusinessContext($outletId);

        // 1. Check low stock
        if ($contextData['low_stock']->count() > 0) {
            AiInsight::create([
                'outlet_id' => $outletId,
                'type' => 'stock_prediction',
                'title' => 'Peringatan Stok Menipis',
                'content' => "Terdapat {$contextData['low_stock']->count()} produk dengan stok menipis: ".
                            $contextData['low_stock']->pluck('name')->join(', '),
                'data' => ['products' => $contextData['low_stock']->toArray()],
                'severity' => 'warning',
                'insight_date' => now(),
            ]);
        }

        // 2. Check sales trend
        if ($contextData['sales_summary']->count() >= 3) {
            $recentSales = $contextData['sales_summary']->take(3)->avg('total_revenue');
            $olderSales = $contextData['sales_summary']->skip(3)->take(3)->avg('total_revenue');

            if ($recentSales < $olderSales * 0.7) { // Penurunan >30%
                AiInsight::create([
                    'outlet_id' => $outletId,
                    'type' => 'anomaly',
                    'title' => 'Penurunan Penjualan Signifikan',
                    'content' => 'Penjualan 3 hari terakhir menurun lebih dari 30% dibanding periode sebelumnya. Perlu investigasi.',
                    'data' => [
                        'recent_avg' => $recentSales,
                        'older_avg' => $olderSales,
                        'decline_percentage' => round((1 - $recentSales / $olderSales) * 100, 2),
                    ],
                    'severity' => 'critical',
                    'insight_date' => now(),
                ]);
            }
        }
    }

    private function formatSalesSummary($salesSummary): string
    {
        if ($salesSummary->isEmpty()) {
            return 'Belum ada data penjualan';
        }

        $formatted = [];
        foreach ($salesSummary as $sale) {
            $date = Carbon::parse($sale->sale_date)->format('d M');
            $revenue = number_format($sale->total_revenue, 0, ',', '.');
            $formatted[] = "- {$date}: {$sale->total_transactions} transaksi, Rp {$revenue}";
        }

        return implode("\n", $formatted);
    }

    private function formatTopProducts($topProducts): string
    {
        if ($topProducts->isEmpty()) {
            return 'Belum ada data produk terlaris';
        }

        $formatted = [];
        foreach ($topProducts as $index => $product) {
            $revenue = number_format($product->total_revenue, 0, ',', '.');
            $formatted[] = ($index + 1).". {$product->name}: {$product->total_sold} terjual (Rp {$revenue})";
        }

        return implode("\n", $formatted);
    }

    private function formatLowStock($lowStock): string
    {
        if ($lowStock->isEmpty()) {
            return 'Semua produk stoknya aman';
        }

        $formatted = [];
        foreach ($lowStock as $product) {
            $formatted[] = "- {$product->name}: Stok {$product->quantity} (min: {$product->min_stock})";
        }

        return implode("\n", $formatted);
    }
}

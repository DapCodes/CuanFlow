<?php

namespace App\Services;

use App\Models\AiChatSession;
use App\Models\AiInsight;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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
        if (!$this->checkAndResetQuota($user)) {
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
                'user_message' => $userMessage
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(60)->post($this->baseUrl . '/chat/completions', [
                'model' => 'anthropic/claude-3.5-sonnet',
                'messages' => $messages,
                'max_tokens' => 2000,
            ]);

            \Log::info('Clara AI Response Status', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (!isset($data['choices'][0]['message']['content'])) {
                    \Log::error('Invalid response structure', ['data' => $data]);
                    return [
                        'success' => false,
                        'message' => 'Format response tidak valid.',
                    ];
                }

                $aiResponse = $data['choices'][0]['message']['content'];
                
                // Save AI response
                $session->addMessage('assistant', $aiResponse);

                // Generate insight jika diperlukan
                $this->generateInsightIfNeeded($session->outlet_id, $userMessage, $aiResponse, $contextData);

                // Kurangi quota
                $user->decrement('daily_chat_quota');

                return [
                    'success' => true,
                    'message' => $aiResponse,
                    'remaining_quota' => $user->daily_chat_quota,
                ];
            }

            \Log::error('Clara AI request failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            return [
                'success' => false,
                'message' => 'Maaf, terjadi kesalahan saat menghubungi Clara AI. Status: ' . $response->status(),
            ];
        } catch (\Exception $e) {
            \Log::error('Clara AI Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
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
        // Dapatkan tanggal data paling lama yang tersedia
        $oldestSaleDate = DB::table('sales')
            ->where('outlet_id', $outletId)
            ->where('status', 'completed')
            ->min('created_at');

        $dataAvailableSince = $oldestSaleDate ? Carbon::parse($oldestSaleDate)->format('d M Y') : null;

        // Sales summary - 7 hari terakhir dengan STRICT outlet filter
        $salesSummary = DB::table('sales')
            ->where('outlet_id', $outletId) // STRICT: hanya outlet ini
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
            ->where('sales.outlet_id', $outletId) // STRICT: hanya outlet ini
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
            ->where('product_stocks.outlet_id', $outletId) // STRICT: hanya outlet ini
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
            : "Belum ada data penjualan";

        $systemPrompt = "Kamu adalah Clara AI, asisten bisnis untuk CuanFlow POS. Jawab dalam Bahasa Indonesia.

PENTING - ATURAN DATA:
- Semua data yang kamu berikan HANYA untuk outlet_id: {$contextData['outlet_id']}
- {$dataInfo}
- Jika user menanyakan data di luar rentang yang tersedia, jawab dengan jelas: \"Maaf, data untuk periode tersebut tidak tersedia. Data penjualan saya hanya tersedia mulai dari {$contextData['data_available_since']}.\"
- JANGAN membuat asumsi atau prediksi untuk data yang tidak ada
- Jika tidak ada data sama sekali, katakan dengan jelas

DATA BISNIS OUTLET INI:
Pendapatan Hari Ini: Rp " . number_format($contextData['today_revenue'], 0, ',', '.') . "

Penjualan 7 Hari Terakhir:
" . json_encode($contextData['sales_summary'], JSON_PRETTY_PRINT) . "

Top 5 Produk Terlaris:
" . json_encode($contextData['top_products'], JSON_PRETTY_PRINT) . "

Produk Stok Menipis:
" . json_encode($contextData['low_stock'], JSON_PRETTY_PRINT) . "

Berikan insight singkat, actionable, dan HANYA berdasarkan data yang tersedia.";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        // Ambil 4 pesan terakhir
        $history = $session->messages()
            ->orderBy('created_at', 'desc')
            ->limit(4)
            ->get()
            ->reverse();

        foreach ($history as $msg) {
            if ($msg->role !== 'system') {
                $messages[] = [
                    'role' => $msg->role,
                    'content' => $msg->content,
                ];
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
        if (!$insightType) {
            if (stripos($aiResponse, 'penting') !== false || 
                stripos($aiResponse, 'perhatian') !== false ||
                stripos($aiResponse, 'segera') !== false) {
                $insightType = 'general';
            }
        }

        if (!$insightType) {
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
            return $baseTitle . ' - ' . $contextData['low_stock']->count() . ' Produk Stok Menipis';
        }

        return $baseTitle . ' - ' . now()->format('d M Y');
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
                'content' => "Terdapat {$contextData['low_stock']->count()} produk dengan stok menipis: " . 
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
}
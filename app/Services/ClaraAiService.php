<?php

namespace App\Services;

use App\Models\AiChatSession;
use App\Models\AiInsight;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
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

        // (Quota check removed) — always allow chat

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

            if (! $this->apiKey) {
                return [
                    'success' => false,
                    'message' => 'API Key Clara AI belum dikonfigurasi. Silakan hubungi administrator.',
                ];
            }

            $attempts = 0;
            $maxAttempts = 2;
            $currentModel = 'arcee-ai/trinity-mini:free';
            $httpResponse = null;

            while ($attempts < $maxAttempts) {
                $attempts++;

                $httpResponse = Http::withHeaders([
                    'Authorization' => 'Bearer '.$this->apiKey,
                    'Content-Type' => 'application/json',
                    'HTTP-Referer' => config('app.url'),
                    'X-Title' => 'CuanFlow POS',
                ])->timeout(120)->post($this->baseUrl.'/chat/completions', [
                    'model' => $currentModel,
                    'messages' => $messages,
                    'max_tokens' => 2000,
                ]);

                \Log::info("Clara AI Attempt $attempts status", [
                    'model' => $currentModel,
                    'status' => $httpResponse->status(),
                ]);

                if ($httpResponse->successful()) {
                    $data = $httpResponse->json();

                    // Jika ada error dari provider (sering terjadi di model free)
                    if (isset($data['error'])) {
                        \Log::warning('Clara AI provider error on model '.$currentModel, ['error' => $data['error']]);

                        if ($attempts < $maxAttempts) {
                            $currentModel = 'google/gemma-3-12b-it:free'; // Fallback ke Gemini yang lebih stabil

                            continue;
                        }

                        return [
                            'success' => false,
                            'message' => 'Maaf, Clara AI sedang sangat sibuk. Silakan coba lagi sebentar lagi.',
                        ];
                    }

                    if (! isset($data['choices'][0]['message']['content'])) {
                        \Log::error('Invalid response structure', ['data' => $data]);

                        return [
                            'success' => false,
                            'message' => 'Format response tidak valid.',
                        ];
                    }

                    $aiResponse = $data['choices'][0]['message']['content'];
                    $cleanResponse = $this->cleanAiResponse($aiResponse);

                    if (trim($cleanResponse) === '') {
                        \Log::warning('Empty AI response', ['raw' => $aiResponse]);

                        return [
                            'success' => false,
                            'message' => 'Maaf, Clara AI tidak bisa menjawab saat ini. Coba ubah pertanyaannya.',
                        ];
                    }

                    $session->addMessage('assistant', $cleanResponse);
                    $this->generateInsightIfNeeded($session->outlet_id, $userMessage, $cleanResponse, $contextData);

                    return [
                        'success' => true,
                        'message' => $cleanResponse,
                    ];
                }

                // Jika status code bukan 2xx (e.g. 429 atau 5xx)
                if ($attempts < $maxAttempts) {
                    $currentModel = 'google/gemma-3-12b-it:free';
                    sleep(1); // Jeda sebelum retry

                    continue;
                }
            }

            return [
                'success' => false,
                'message' => 'Maaf, terjadi kesalahan saat menghubungi Clara AI. Status: '.$httpResponse->status(),
            ];

            \Log::error('Clara AI request failed', [
                'status' => $httpResponse->status(),
                'body' => $httpResponse->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Maaf, terjadi kesalahan saat menghubungi Clara AI. Status: '.$httpResponse->status(),
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
        // 0. Hapus pemikiran (thoughts) dari DeepSeek R1 jika ada
        $response = preg_replace('/<think>.*?<\/think>/s', '', $response);

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

        // 5. Hapus markdown code blocks (```code```) tapi simpan isinya
        // Sebelumnya dihapus total, sekarang cuma buang ``` di luar
        $response = preg_replace('/```(.*?)```/s', '$1', $response);

        // 6. Hapus inline code `code`
        $response = preg_replace('/`([^`]+)`/', '$1', $response);

        // 7. Normalize multiple spaces menjadi single space
        $response = preg_replace('/[ \t]+/', ' ', $response);

        // 8. Normalize multiple newlines (max 2 newlines)
        $response = preg_replace('/\n{3,}/', "\n\n", $response);

        // 9. Trim setiap baris
        $lines = explode("\n", $response);
        $lines = array_map('trim', $lines);
        $response = implode("\n", $lines);

        // 10. Final trim
        return trim($response);
    }

    // quota logic removed — no daily limit enforced

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

            // SKIP kalau content kosong / cuma spasi
            if (trim($msg->content) === '') {
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
            'is_read' => true,
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

        // DEFAULT insight kalau ada penjualan hari ini
        if (($contextData['today_revenue'] ?? 0) > 0) {
            AiInsight::create([
                'outlet_id' => $outletId,
                'type' => 'sales_trend',
                'title' => 'Ringkasan Penjualan Hari Ini',
                'content' => 'Pendapatan hari ini: Rp '.number_format($contextData['today_revenue'], 0, ',', '.').
                            '. Total transaksi 7 hari terakhir bisa dicek di dashboard penjualan.',
                'data' => [
                    'today_revenue' => $contextData['today_revenue'],
                    'top_products' => $contextData['top_products']->toArray(),
                    'low_stock' => $contextData['low_stock']->toArray(),
                ],
                'severity' => 'info',
                'insight_date' => now(),
            ]);
        }

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

    public function generateInsightIfNeededOnOnline(int $outletId): void
    {
        // 1. Cek: sudah ada insight < 24 jam?
        $alreadyGenerated = AiInsight::where('outlet_id', $outletId)
            ->where('created_at', '>=', now()->subHours(24))
            ->exists();

        if ($alreadyGenerated) {
            return;
        }

        // 2. Syarat: sales > 0 (hari ini)
        $salesCount = DB::table('sales')
            ->where('outlet_id', $outletId)
            ->where('status', 'completed')
            ->whereDate('created_at', today())
            ->count();

        if ($salesCount <= 0) {
            return;
        }

        // 3. Generate insight otomatis
        $this->generateDailyInsights($outletId);
    }

    // =====================================================================
    // AI STUDIO — Multi-Mode Prompt Generation
    // =====================================================================

    /**
     * Get user's outlet business data with caching.
     */
    public function getUserOutletData($userId): array
    {
        $user = User::findOrFail($userId);
        $outletId = $user->outlet_id;

        if (! $outletId) {
            return ['error' => 'User tidak memiliki outlet.'];
        }

        return Cache::remember("clara_studio_outlet_{$outletId}", now()->addMinutes(15), function () use ($outletId) {
            $outlet = DB::table('outlets')->where('id', $outletId)->first();

            // Products catalog
            $products = DB::table('products')
                ->where('outlet_id', $outletId)
                ->where('is_active', true)
                ->select('name', 'selling_price', 'hpp', 'description', 'category_id')
                ->limit(30)
                ->get();

            // Top selling products (30 days)
            $topProducts = DB::table('sale_items')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->join('products', 'sale_items.product_id', '=', 'products.id')
                ->where('sales.outlet_id', $outletId)
                ->where('sales.status', 'completed')
                ->where('sales.created_at', '>=', now()->subDays(30))
                ->selectRaw('products.name, SUM(sale_items.quantity) as total_sold, SUM(sale_items.subtotal) as total_revenue')
                ->groupBy('products.id', 'products.name')
                ->orderBy('total_sold', 'desc')
                ->limit(10)
                ->get();

            // Revenue stats (30 days)
            $revenueStats = DB::table('sales')
                ->where('outlet_id', $outletId)
                ->where('status', 'completed')
                ->where('created_at', '>=', now()->subDays(30))
                ->selectRaw('COUNT(*) as total_transactions, SUM(grand_total) as total_revenue, AVG(grand_total) as avg_transaction')
                ->first();

            // Customer demographics
            $customerStats = DB::table('sales')
                ->where('outlet_id', $outletId)
                ->where('status', 'completed')
                ->where('created_at', '>=', now()->subDays(30))
                ->selectRaw('COUNT(DISTINCT customer_id) as unique_customers')
                ->first();

            return [
                'outlet_name' => $outlet->name ?? 'Outlet',
                'outlet_id' => $outletId,
                'products' => $products,
                'top_products' => $topProducts,
                'revenue_stats' => $revenueStats,
                'customer_stats' => $customerStats,
            ];
        });
    }

    /**
     * Main entry point for AI Studio generation.
     */
    public function generate(string $mode, string $userPrompt, int $userId, array $options = []): array
    {
        $validModes = ['video_prompt', 'affiliate_script', 'ads_image_prompt', 'kalkulaba'];

        if (! in_array($mode, $validModes)) {
            return [
                'success' => false,
                'message' => 'Mode tidak valid. Pilih: video_prompt, affiliate_script, atau ads_image_prompt.',
            ];
        }

        $outletData = $this->getUserOutletData($userId);

        if (isset($outletData['error'])) {
            return [
                'success' => false,
                'message' => $outletData['error'],
            ];
        }

        $tone = $options['tone'] ?? 'casual';
        $language = $options['language'] ?? 'id';

        $cleanPrompt = $this->sanitizeInput($userPrompt);
        $enrichedPrompt = $this->enrichPrompt($outletData, $cleanPrompt, $language);

        try {
            $result = match ($mode) {
                'video_prompt' => $this->generateVideoPrompt($outletData, $enrichedPrompt, $tone, $language),
                'affiliate_script' => $this->generateAffiliateScript($outletData, $enrichedPrompt, $tone, $language),
                'ads_image_prompt' => $this->generateAdsImagePrompt($outletData, $enrichedPrompt, $tone, $language),
                'kalkulaba' => $this->generateKalkulaba($outletData, $cleanPrompt, $tone, $language, $options),
            };

            if (! $result['success']) {
                return $result;
            }

            return [
                'success' => true,
                'mode' => $mode,
                'result' => $result['content'],
                'data_used' => [
                    'outlet_name' => $outletData['outlet_name'],
                    'products_count' => $outletData['products']->count(),
                    'top_products' => $outletData['top_products']->pluck('name')->toArray(),
                ],
            ];
        } catch (\Exception $e) {
            \Log::error('Clara AI Studio Exception', [
                'mode' => $mode,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghasilkan konten: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Generate cinematic video prompt.
     */
    private function generateVideoPrompt(array $data, string $userPrompt, string $tone, string $language): array
    {
        $productContext = $this->formatProductContext($data);
        $langInstruction = $language === 'en'
            ? 'CRITICAL LANGUAGE RULE: You MUST write your ENTIRE response in English. Every single word, sentence, heading, and description must be in English. Do NOT use any Indonesian/Bahasa Indonesia whatsoever.'
            : 'CRITICAL LANGUAGE RULE: Kamu WAJIB menulis SELURUH respons dalam Bahasa Indonesia. Setiap kata, kalimat, heading, dan deskripsi harus dalam Bahasa Indonesia. JANGAN gunakan Bahasa Inggris sama sekali kecuali istilah teknis.';
        $toneInstruction = $this->getToneInstruction($tone, 'video');

        $systemPrompt = "You are a world-class AI video prompt engineer specializing in creating highly detailed, cinematic prompts for AI video generation tools (Runway Gen-3, Sora, Pika Labs, Kling).

{$langInstruction}
{$toneInstruction}

BUSINESS CONTEXT for {$data['outlet_name']}:
{$productContext}

YOUR TASK:
Generate a structured, highly optimized video production prompt based on the user's request. Your output MUST include ALL of the following sections clearly labeled:

1. **SCENE BREAKDOWN** — Describe each scene in detail (Scene 1, Scene 2, etc.)
2. **CAMERA MOVEMENT** — Specific camera techniques (dolly, crane, tracking shot, close-up, wide, etc.)
3. **LIGHTING** — Lighting setup (golden hour, studio, neon, natural, cinematic, etc.)
4. **MOOD & TONE** — Overall emotional atmosphere
5. **SUBJECT DETAILS** — Detailed description of the main subject/product
6. **ENVIRONMENT** — Background, setting, location details
7. **STYLE REFERENCE** — Visual style (commercial, documentary, cinematic, social media, etc.)
8. **AI VIDEO TOOL KEYWORDS** — Comma-separated keywords optimized for AI video tools

Make the prompt extremely detailed and visual. Each scene should be a paragraph of description. Think like a film director.

REMINDER: {$langInstruction}";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ];

        return $this->callAI($messages);
    }

    /**
     * Generate high-converting affiliate script.
     */
    private function generateAffiliateScript(array $data, string $userPrompt, string $tone, string $language): array
    {
        $productContext = $this->formatProductContext($data);
        $langInstruction = $language === 'en'
            ? 'CRITICAL LANGUAGE RULE: You MUST write your ENTIRE response in English. Every single word, sentence, heading, and description must be in English. Do NOT use any Indonesian/Bahasa Indonesia whatsoever.'
            : 'CRITICAL LANGUAGE RULE: Kamu WAJIB menulis SELURUH respons dalam Bahasa Indonesia. Setiap kata, kalimat, heading, dan deskripsi harus dalam Bahasa Indonesia. JANGAN gunakan Bahasa Inggris sama sekali kecuali istilah teknis.';
        $toneInstruction = $this->getToneInstruction($tone, 'affiliate');

        $bestSeller = $data['top_products']->first();
        $bestSellerInfo = $bestSeller
            ? "Best-selling product: {$bestSeller->name} ({$bestSeller->total_sold} units sold, Rp " . number_format($bestSeller->total_revenue, 0, ',', '.') . " revenue)"
            : 'No sales data available yet.';

        $systemPrompt = "You are an elite affiliate marketing copywriter and social media script writer with expertise in viral content creation for TikTok, Instagram Reels, and YouTube Shorts.

{$langInstruction}
{$toneInstruction}

BUSINESS CONTEXT for {$data['outlet_name']}:
{$productContext}
{$bestSellerInfo}

YOUR TASK:
Generate a complete, high-converting affiliate/promotional script based on the user's request. Your output MUST include ALL of the following sections clearly labeled:

1. **HOOK** (0-3 seconds) — An attention-grabbing opening line that stops the scroll. Use curiosity, controversy, or shock value.
2. **PROBLEM STATEMENT** — Identify and amplify the pain point the target audience faces.
3. **PRODUCT INTRODUCTION** — Naturally introduce the product/brand as the solution. Include specific details from the business data.
4. **BENEFITS** — List 3-5 compelling benefits as bullet points. Use specific numbers and results when possible.
5. **SOCIAL PROOF** — Add credibility elements (bestseller data, customer count, ratings, testimonials framework).
6. **CALL TO ACTION (CTA)** — Clear, urgent, compelling action step.
7. **PLATFORM ADAPTATIONS:**
   - **TikTok Version** (15-30 sec script)
   - **Instagram Reels Version** (30-60 sec script)
   - **YouTube Shorts Version** (30-60 sec script)

Each version should feel native to the platform. Use real product names and pricing from the business data.

REMINDER: {$langInstruction}";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ];

        return $this->callAI($messages);
    }

    /**
     * Generate AI image prompt for ads.
     */
    private function generateAdsImagePrompt(array $data, string $userPrompt, string $tone, string $language): array
    {
        $productContext = $this->formatProductContext($data);
        $langInstruction = $language === 'en'
            ? 'CRITICAL LANGUAGE RULE: You MUST write your ENTIRE response in English. Every single word, sentence, heading, and description must be in English. Do NOT use any Indonesian/Bahasa Indonesia whatsoever.'
            : 'CRITICAL LANGUAGE RULE: Kamu WAJIB menulis SELURUH respons dalam Bahasa Indonesia. Setiap kata, kalimat, heading, dan deskripsi harus dalam Bahasa Indonesia. JANGAN gunakan Bahasa Inggris sama sekali kecuali istilah teknis.';
        $toneInstruction = $this->getToneInstruction($tone, 'ads_image');

        $systemPrompt = "You are an expert advertising creative director and AI image prompt engineer specializing in DALL·E 3, Midjourney, and Stable Diffusion XL (SDXL).

{$langInstruction}
{$toneInstruction}

BUSINESS CONTEXT for {$data['outlet_name']}:
{$productContext}

YOUR TASK:
Generate highly optimized image generation prompts for advertising materials based on the user's request. Your output MUST include ALL of the following sections clearly labeled:

1. **VISUAL COMPOSITION** — Layout, framing, rule of thirds, focal point placement.
2. **SUBJECT & FOCUS** — Detailed description of the main subject/product appearance.
3. **BACKGROUND** — Background environment, props, setting details.
4. **LIGHTING** — Specific lighting setup (studio, natural, dramatic, flat lay, etc.)
5. **COLOR GRADING** — Color palette, mood, saturation, contrast.
6. **BRANDING STYLE** — Visual brand identity direction (minimal, luxury, street, artisan, etc.)
7. **MARKETING ANGLE** — Choose one: luxury, urgency, discount, exclusivity, lifestyle, FOMO, social proof.
8. **TEXT OVERLAY SUGGESTION** — Headline text, subtext, CTA text, font style recommendation.
9. **READY-TO-USE PROMPTS:**
   - **Midjourney Prompt** — Full prompt with parameters (--ar, --style, --v)
   - **DALL·E 3 Prompt** — Optimized natural language prompt
   - **SDXL Prompt** — Prompt with positive/negative prompt structure

Each prompt should be production-ready and specifically reference the business/product context.

REMINDER: {$langInstruction}";

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ];

        return $this->callAI($messages);
    }

    /**
     * Generate Kalkulaba AI — Cost Analysis & Pricing Strategy for UMKM.
     * Supports multimodal image analysis and dynamic cost categories.
     */
    private function generateKalkulaba(array $data, string $userPrompt, string $tone, string $language, array $options = []): array
    {
        $productContext = $this->formatProductContext($data);
        $langInstruction = $language === 'en'
            ? 'CRITICAL LANGUAGE RULE: You MUST write your ENTIRE response in English. Every single word must be in English.'
            : 'CRITICAL LANGUAGE RULE: Kamu WAJIB menulis SELURUH respons dalam Bahasa Indonesia. JANGAN gunakan Bahasa Inggris kecuali istilah teknis.';

        // Extract options
        $productName = $options['product_name'] ?? '';
        $productDescription = $options['product_description'] ?? '';
        $imageUrl = $options['image_url'] ?? '';
        $imageBase64 = $options['image_base64'] ?? '';
        $businessType = $options['business_type'] ?? 'food';
        $additionalCosts = $options['additional_costs'] ?? [];
        $targetProfit = $options['target_profit'] ?? 0;
        $advancedMode = $options['advanced_mode'] ?? '';

        // Build dynamic cost context from user-defined categories
        $costContext = '';
        if (!empty($additionalCosts) && is_array($additionalCosts)) {
            $parts = [];
            foreach ($additionalCosts as $cost) {
                $label = $cost['label'] ?? '';
                $value = (int) ($cost['value'] ?? 0);
                if ($label && $value > 0) {
                    $parts[] = "{$label}: Rp " . number_format($value, 0, ',', '.');
                }
            }
            if (!empty($parts)) {
                $costContext = "\nUSER-PROVIDED ADDITIONAL COSTS (include these in COGS breakdown):\n" . implode("\n", $parts);
            }
        }

        $advancedInstruction = '';
        if ($advancedMode === 'exclusive') {
            $advancedInstruction = "\n\nADVANCED MODE: EXCLUSIVE\n- Enhance branding suggestions\n- Suggest premium packaging ideas\n- Suggest upselling strategies\n- Focus on perceived luxury value";
        } elseif ($advancedMode === 'efficiency') {
            $advancedInstruction = "\n\nADVANCED MODE: EFFICIENCY\n- Focus on minimizing production cost\n- Suggest cheaper ingredient alternatives\n- Optimize portion sizes for cost savings\n- Prioritize volume-based strategies";
        }

        $hasImage = !empty($imageBase64) || !empty($imageUrl);

        $imageInstruction = $hasImage
            ? "\n\nIMAGE ANALYSIS INSTRUCTION:\nA product image has been provided. You MUST analyze it visually to identify:\n- Type of food/product\n- Visible ingredients\n- Portion size estimation\n- Presentation quality (street food, casual, premium)\n- Packaging type\nUse this visual analysis to generate an accurate recipe and cost estimate."
            : '';

        $systemPrompt = "You are Kalkulaba AI, an advanced business calculator and cost analysis AI system designed specifically for Indonesian small businesses (UMKM). You help business owners calculate profit, generate product recipes, and predict revenue targets.

{$langInstruction}

BUSINESS CONTEXT for {$data['outlet_name']}:
{$productContext}

PRODUCT TO ANALYZE:
- Name: {$productName}
- Description: {$productDescription}
- Business Type: {$businessType}" .
($imageUrl && !$imageBase64 ? "\n- Image URL: {$imageUrl}" : '') .
"\n- Target Profit: Rp " . number_format($targetProfit, 0, ',', '.') .
$costContext .
$advancedInstruction .
$imageInstruction . "

YOUR TASK — Follow these steps precisely:

1. PRODUCT ANALYSIS: Analyze the product" . ($hasImage ? ' image and' : '') . " description. Identify category, likely ingredients, target market (low-end/mid/premium).

2. RECIPE GENERATION (for food/beverage): Generate a realistic, cost-efficient recipe with:
   - Ingredient list with estimated quantities per portion
   - Brief preparation steps
   - Estimated portion yield
   If NOT food/beverage, skip recipe and list raw materials/components instead.

3. COST ESTIMATION (COGS): Calculate total cost per unit:
   - Ingredient/material costs (use realistic Indonesian local market prices in Rupiah)
   - Include ALL user-provided additional costs in the breakdown (use the exact labels the user provided)
   - Show complete breakdown

4. PRICING STRATEGY: Generate 3 pricing tiers:
   - LOW: 5-15% margin (high volume target)
   - COMPETITIVE: 20-40% margin (balanced)
   - EXCLUSIVE: 50-100%+ margin (premium branding)
   For each: selling price, profit per unit, margin percentage

5. PROFIT TARGET: For each pricing tier, calculate units_needed = target_profit / profit_per_unit (round UP)

6. SMART INSIGHTS: Give 3 very concise, actionable insights (max 20 words each):
   - Most realistic pricing strategy
   - Risk analysis
   - Cost reduction suggestions
   - Value improvement ideas

CRITICAL: You MUST respond with ONLY valid JSON. No markdown, no explanation outside JSON. Use this EXACT structure:

{\"recipe\":{\"ingredients\":[{\"name\":\"string\",\"quantity\":\"string\",\"estimated_cost\":0}],\"steps\":[\"string\"],\"estimated_cost\":0},\"cost_analysis\":{\"cogs_per_unit\":0,\"breakdown\":[{\"label\":\"string\",\"value\":0}]},\"pricing\":{\"low\":{\"price\":0,\"profit_per_unit\":0,\"margin\":0,\"units_to_target\":0},\"competitive\":{\"price\":0,\"profit_per_unit\":0,\"margin\":0,\"units_to_target\":0},\"exclusive\":{\"price\":0,\"profit_per_unit\":0,\"margin\":0,\"units_to_target\":0}},\"insights\":[\"string\"]}

IMPORTANT for cost_analysis.breakdown:
- It MUST be an ARRAY of objects with \"label\" and \"value\" keys
- First item should be \"Bahan Baku\" (total ingredients cost)
- Then include each user-provided cost category with their exact label
- Example: [{\"label\":\"Bahan Baku\",\"value\":5000},{\"label\":\"Gas\",\"value\":500},{\"label\":\"Packaging\",\"value\":1000}]

- ALL monetary values must be in Indonesian Rupiah (integer, no decimals)
- Margin values as percentage numbers (e.g. 25 for 25%)
- Keep calculations logical and realistic for small businesses
- Do NOT hallucinate extreme numbers
- If data is missing, estimate intelligently based on Indonesian market prices
- RESPONSE MUST BE ONLY THE JSON OBJECT.
- NO MARKDOWN, NO CODE BLOCKS, NO 'Here is the JSON', NO 'I hope this helps'.
- JUST THE RAW JSON STARTING WITH { AND ENDING WITH }.";

        // Build messages with multimodal support for image analysis
        if (!empty($imageBase64)) {
            // Use multimodal format with base64 image for vision models
            $userContent = [
                ['type' => 'text', 'text' => $userPrompt],
                [
                    'type' => 'image_url',
                    'image_url' => ['url' => $imageBase64],
                ],
            ];

            $messages = [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userContent],
            ];

            // Force vision-capable model for image analysis
            return $this->callAIWithModel($messages, 'openrouter/free');
        }

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ];

        return $this->callAI($messages);
    }

    /**
     * Enrich user prompt with business context.
     */
    private function enrichPrompt(array $data, string $userPrompt, string $language = 'id'): string
    {
        $topProductNames = $data['top_products']->pluck('name')->implode(', ');
        $avgTransaction = $data['revenue_stats']->avg_transaction ?? 0;

        $context = [];

        if ($language === 'en') {
            $context[] = "Business: {$data['outlet_name']}";
            if ($topProductNames) {
                $context[] = "Top products: {$topProductNames}";
            }
            if ($avgTransaction > 0) {
                $context[] = "Average transaction: Rp " . number_format($avgTransaction, 0, ',', '.');
            }
            $contextSuffix = "\n\n[Business context: " . implode(' | ', $context) . "]";
        } else {
            $context[] = "Bisnis: {$data['outlet_name']}";
            if ($topProductNames) {
                $context[] = "Produk unggulan: {$topProductNames}";
            }
            if ($avgTransaction > 0) {
                $context[] = "Rata-rata transaksi: Rp " . number_format($avgTransaction, 0, ',', '.');
            }
            $contextSuffix = "\n\n[Konteks bisnis: " . implode(' | ', $context) . "]";
        }

        return $userPrompt . $contextSuffix;
    }

    /**
     * Format product catalog as context string.
     */
    private function formatProductContext(array $data): string
    {
        $lines = [];

        if ($data['products']->isNotEmpty()) {
            $lines[] = "PRODUCT CATALOG:";
            foreach ($data['products']->take(15) as $p) {
                $price = number_format($p->selling_price, 0, ',', '.');
                $desc = $p->description ? " — {$p->description}" : '';
                $lines[] = "- {$p->name}: Rp {$price}{$desc}";
            }
        }

        if ($data['top_products']->isNotEmpty()) {
            $lines[] = "\nTOP SELLING PRODUCTS (last 30 days):";
            foreach ($data['top_products']->take(5) as $tp) {
                $rev = number_format($tp->total_revenue, 0, ',', '.');
                $lines[] = "- {$tp->name}: {$tp->total_sold} sold (Rp {$rev})";
            }
        }

        if ($data['revenue_stats']) {
            $totalRev = number_format($data['revenue_stats']->total_revenue ?? 0, 0, ',', '.');
            $totalTx = $data['revenue_stats']->total_transactions ?? 0;
            $lines[] = "\nBUSINESS STATS (30 days): {$totalTx} transactions, Rp {$totalRev} revenue";
        }

        if ($data['customer_stats']) {
            $lines[] = "Unique customers (30 days): " . ($data['customer_stats']->unique_customers ?? 0);
        }

        return implode("\n", $lines) ?: 'No business data available.';
    }

    /**
     * Get tone instruction for prompts.
     */
    private function getToneInstruction(string $tone, string $context): string
    {
        return match ($tone) {
            'formal' => 'TONE: Professional, polished, corporate. Use sophisticated vocabulary and structured language.',
            'aggressive' => 'TONE: Bold, urgent, high-energy marketing. Use power words, scarcity tactics, and strong emotional triggers.',
            default => 'TONE: Friendly, conversational, approachable. Use casual language that feels relatable and authentic.',
        };
    }

    /**
     * Sanitize user input.
     */
    private function sanitizeInput(string $input): string
    {
        $input = strip_tags($input);
        $input = preg_replace('/\s+/', ' ', $input);
        $input = trim($input);

        return mb_substr($input, 0, 2000);
    }

    /**
     * Call AI API with retry and fallback logic.
     */
    private function callAI(array $messages): array
    {
        if (! $this->apiKey) {
            return [
                'success' => false,
                'message' => 'API Key Clara AI belum dikonfigurasi. Silakan hubungi administrator.',
            ];
        }

        $attempts = 0;
        $maxAttempts = 2;
        $currentModel = 'arcee-ai/trinity-mini:free';
        $httpResponse = null;

        while ($attempts < $maxAttempts) {
            $attempts++;

            $httpResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('app.url'),
                'X-Title' => 'CuanFlow POS - AI Studio',
            ])->timeout(120)->post($this->baseUrl . '/chat/completions', [
                'model' => $currentModel,
                'messages' => $messages,
                'max_tokens' => 6000,
            ]);

            if ($httpResponse->successful()) {
                $responseData = $httpResponse->json();

                if (isset($responseData['error'])) {
                    \Log::warning('Clara AI Studio provider error on model ' . $currentModel, ['error' => $responseData['error']]);

                    if ($attempts < $maxAttempts) {
                        $currentModel = 'google/gemma-3-12b-it:free';
                        continue;
                    }

                    return [
                        'success' => false,
                        'message' => 'Clara AI sedang sibuk. Silakan coba lagi sebentar lagi.',
                    ];
                }

                if (! isset($responseData['choices'][0]['message']['content'])) {
                    return [
                        'success' => false,
                        'message' => 'Format response AI tidak valid.',
                    ];
                }

                $content = $responseData['choices'][0]['message']['content'];
                $content = preg_replace('/<think>.*?<\/think>/s', '', $content);
                $content = trim($content);

                if ($content === '') {
                    return [
                        'success' => false,
                        'message' => 'AI tidak dapat menghasilkan konten. Coba ubah prompt Anda.',
                    ];
                }

                return [
                    'success' => true,
                    'content' => $content,
                ];
            }

            if ($attempts < $maxAttempts) {
                $currentModel = 'google/gemma-3-12b-it:free';
                sleep(1);
                continue;
            }
        }

        return [
            'success' => false,
            'message' => 'Gagal menghubungi AI. Status: ' . ($httpResponse ? $httpResponse->status() : 'unknown'),
        ];
    }

    /**
     * Call AI API with a specific model (no fallback). Used for vision/multimodal.
     */
    private function callAIWithModel(array $messages, string $model): array
    {
        if (! $this->apiKey) {
            return [
                'success' => false,
                'message' => 'API Key Clara AI belum dikonfigurasi. Silakan hubungi administrator.',
            ];
        }

        $httpResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'HTTP-Referer' => config('app.url'),
            'X-Title' => 'CuanFlow POS - Kalkulaba AI',
        ])->timeout(180)->post($this->baseUrl . '/chat/completions', [
            'model' => $model,
            'messages' => $messages,
            'max_tokens' => 8000,
        ]);

        if ($httpResponse->successful()) {
            $responseData = $httpResponse->json();

            if (isset($responseData['error'])) {
                \Log::warning('Kalkulaba AI vision model error', ['error' => $responseData['error']]);

                return [
                    'success' => false,
                    'message' => 'AI vision model sedang sibuk. Silakan coba lagi.',
                ];
            }

            if (! isset($responseData['choices'][0]['message']['content'])) {
                return [
                    'success' => false,
                    'message' => 'Format response AI tidak valid.',
                ];
            }

            $content = $responseData['choices'][0]['message']['content'];
            $content = preg_replace('/<think>.*?<\/think>/s', '', $content);
            $content = trim($content);

            if ($content === '') {
                return [
                    'success' => false,
                    'message' => 'AI tidak dapat menghasilkan konten. Coba ubah prompt Anda.',
                ];
            }

            return [
                'success' => true,
                'content' => $content,
            ];
        }

        return [
            'success' => false,
            'message' => 'Gagal menghubungi AI vision. Status: ' . $httpResponse->status(),
        ];
    }
}

<?php

namespace App\Services;

use App\Models\AiChatSession;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

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

        // Get business context
        $context = $this->getBusinessContext($session->outlet_id);

        // Build conversation history
        $messages = $this->buildMessages($session, $context, $userMessage);

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

private function getBusinessContext($outletId): string
{
    // Sales summary - kurangi dari 30 hari ke 7 hari
    $salesSummary = DB::table('sales')
        ->where('outlet_id', $outletId)
        ->where('status', 'completed')
        ->selectRaw('
            COUNT(*) as total_transactions,
            SUM(grand_total) as total_revenue,
            AVG(grand_total) as avg_transaction,
            DATE(created_at) as sale_date
        ')
        ->groupBy('sale_date')
        ->orderBy('sale_date', 'desc')
        ->limit(7) // ← ubah dari 30 ke 7
        ->get();

    // Top products - kurangi dari 10 ke 5
    $topProducts = DB::table('sale_items')
        ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
        ->join('products', 'sale_items.product_id', '=', 'products.id')
        ->where('sales.outlet_id', $outletId)
        ->where('sales.status', 'completed')
        ->selectRaw('
            products.name,
            SUM(sale_items.quantity) as total_sold,
            SUM(sale_items.subtotal) as total_revenue
        ')
        ->groupBy('products.id', 'products.name')
        ->orderBy('total_sold', 'desc')
        ->limit(5) // ← ubah dari 10 ke 5
        ->get();

    // Low stock products - tetap
    $lowStock = DB::table('products')
        ->join('product_stocks', 'products.id', '=', 'product_stocks.product_id')
        ->where('product_stocks.outlet_id', $outletId)
        ->whereRaw('product_stocks.quantity <= products.min_stock')
        ->select('products.name', 'product_stocks.quantity', 'products.min_stock')
        ->limit(5) // ← tambahkan limit
        ->get();

    // Persingkat context
    $context = "Data Bisnis:\n\n";
    $context .= "Penjualan 7 hari:\n" . json_encode($salesSummary) . "\n\n";
    $context .= "Top 5 Produk:\n" . json_encode($topProducts) . "\n\n";
    $context .= "Stok Menipis:\n" . json_encode($lowStock) . "\n";

    return $context;
}

private function buildMessages(AiChatSession $session, string $context, string $userMessage): array
{
    $systemPrompt = "Kamu adalah Clara AI, asisten bisnis untuk CuanFlow POS. Jawab dalam Bahasa Indonesia.

Data bisnis:
{$context}

Berikan insight singkat dan actionable.";

    $messages = [
        ['role' => 'system', 'content' => $systemPrompt],
    ];

    // Kurangi history dari 10 ke 4
    $history = $session->messages()
        ->orderBy('created_at', 'desc')
        ->limit(4) // ← ubah dari 10 ke 4
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

}
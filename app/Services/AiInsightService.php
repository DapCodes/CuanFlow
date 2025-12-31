<?php

namespace App\Services;

use App\Models\AiInsight;
use App\Models\CashRegister;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AiInsightService
{
    /**
     * Generate daily insights after store closing
     */
    public function generateDailyInsight(CashRegister $register): ?AiInsight
    {
        $outletId = $register->outlet_id;
        $startDate = $register->opened_at;
        $endDate = $register->closed_at ?? now();

        // Ambil data penjualan hari ini
        $sales = Sale::where('outlet_id', $outletId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['items.product'])
            ->get();

        if ($sales->isEmpty()) {
            return null; // Tidak ada penjualan, skip insight
        }

        // Analisis data
        $analysisData = $this->analyzeSalesData($sales, $outletId, $startDate, $endDate);

        // Generate insight content
        $content = $this->generateInsightContent($analysisData);

        // Simpan insight
        $insight = AiInsight::create([
            'outlet_id' => $outletId,
            'type' => 'sales_trend',
            'title' => 'Ringkasan Penjualan ' . $startDate->format('d M Y'),
            'content' => $content,
            'data' => $analysisData,
            'severity' => $this->determineSeverity($analysisData),
            'insight_date' => $startDate,
        ]);

        return $insight;
    }

    /**
     * Analyze sales data
     */
    private function analyzeSalesData($sales, $outletId, $startDate, $endDate): array
    {
        $totalSales = $sales->sum('grand_total');
        $totalTransactions = $sales->count();
        $totalDiscount = $sales->sum('discount_amount');
        $averageTransaction = $totalTransactions > 0 ? $totalSales / $totalTransactions : 0;

        // Produk terlaris
        $topProducts = SaleItem::whereIn('sale_id', $sales->pluck('id'))
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->with(['product.unit']) // Load unit relation
            ->get();

        // Produk paling rendah penjualannya
        $lowProducts = SaleItem::whereIn('sale_id', $sales->pluck('id'))
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('product_id')
            ->orderBy('total_qty', 'asc')
            ->limit(3)
            ->with(['product.unit']) // Load unit relation
            ->get();

        // Analisis per jam
        $hourlyAnalysis = $this->analyzeHourlyPattern($sales);

        // Bandingkan dengan hari sebelumnya
        $previousDayComparison = $this->compareWithPreviousDay($outletId, $startDate, $totalSales, $totalTransactions);

        // Rekomendasi
        $recommendations = $this->generateRecommendations($topProducts, $lowProducts, $hourlyAnalysis, $previousDayComparison);

        return [
            'summary' => [
                'total_sales' => $totalSales,
                'total_transactions' => $totalTransactions,
                'total_discount' => $totalDiscount,
                'average_transaction' => $averageTransaction,
            ],
            'top_products' => $topProducts->map(fn($item) => [
                'name' => $item->product->name ?? 'Unknown',
                'quantity' => (float) $item->total_qty, // Cast to float to remove trailing zeros
                'unit' => $item->product->unit->name ?? 'unit',
                'revenue' => $item->total_revenue,
            ])->toArray(),
            'low_products' => $lowProducts->map(fn($item) => [
                'name' => $item->product->name ?? 'Unknown',
                'quantity' => (float) $item->total_qty, // Cast to float to remove trailing zeros
                'unit' => $item->product->unit->name ?? 'unit',
                'revenue' => $item->total_revenue,
            ])->toArray(),
            'hourly_analysis' => $hourlyAnalysis,
            'previous_day_comparison' => $previousDayComparison,
            'recommendations' => $recommendations,
        ];
    }

    /**
     * Analyze hourly sales pattern
     */
    private function analyzeHourlyPattern($sales): array
    {
        $hourlyData = $sales->groupBy(function ($sale) {
            return $sale->created_at->format('H');
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('grand_total'),
            ];
        })->sortKeys();

        // Tentukan jam ramai dan sepi
        $peakHour = $hourlyData->sortByDesc('count')->first();
        $quietHour = $hourlyData->sortBy('count')->first();

        $peakHourKey = $hourlyData->search($peakHour);
        $quietHourKey = $hourlyData->search($quietHour);

        return [
            'peak_hour' => [
                'hour' => $peakHourKey,
                'transactions' => $peakHour['count'] ?? 0,
                'revenue' => $peakHour['total'] ?? 0,
            ],
            'quiet_hour' => [
                'hour' => $quietHourKey,
                'transactions' => $quietHour['count'] ?? 0,
                'revenue' => $quietHour['total'] ?? 0,
            ],
            'hourly_breakdown' => $hourlyData->toArray(),
        ];
    }

    /**
     * Compare with previous day
     */
    private function compareWithPreviousDay($outletId, $currentDate, $currentSales, $currentTransactions): array
    {
        $previousDate = Carbon::parse($currentDate)->subDay();
        $previousDayStart = $previousDate->copy()->startOfDay();
        $previousDayEnd = $previousDate->copy()->endOfDay();

        $previousSales = Sale::where('outlet_id', $outletId)
            ->where('status', 'completed')
            ->whereBetween('created_at', [$previousDayStart, $previousDayEnd])
            ->get();

        if ($previousSales->isEmpty()) {
            return [
                'available' => false,
                'message' => 'Tidak ada data hari sebelumnya untuk dibandingkan',
            ];
        }

        $previousTotal = $previousSales->sum('grand_total');
        $previousCount = $previousSales->count();

        $salesDiff = $currentSales - $previousTotal;
        $salesPercentage = $previousTotal > 0 ? (($salesDiff / $previousTotal) * 100) : 0;

        $transactionDiff = $currentTransactions - $previousCount;
        $transactionPercentage = $previousCount > 0 ? (($transactionDiff / $previousCount) * 100) : 0;

        return [
            'available' => true,
            'previous_sales' => $previousTotal,
            'previous_transactions' => $previousCount,
            'sales_difference' => $salesDiff,
            'sales_percentage' => round($salesPercentage, 1),
            'transaction_difference' => $transactionDiff,
            'transaction_percentage' => round($transactionPercentage, 1),
        ];
    }

    /**
     * Generate recommendations
     */
    private function generateRecommendations($topProducts, $lowProducts, $hourlyAnalysis, $previousDayComparison): array
    {
        $recommendations = [];

        // Rekomendasi stok produk terlaris
        if ($topProducts->isNotEmpty()) {
            $topProduct = $topProducts->first();
            $qty = (float) $topProduct->total_qty;
            $unit = $topProduct->product->unit->name ?? 'unit';
            
            $recommendations[] = [
                'type' => 'stock',
                'priority' => 'high',
                'title' => 'Pastikan Stok Produk Terlaris',
                'message' => "Produk '{$topProduct->product->name}' terjual {$qty} {$unit}. Pastikan stok cukup untuk besok.",
            ];
        }

        // Rekomendasi untuk produk rendah penjualan
        if ($lowProducts->isNotEmpty()) {
            $lowProduct = $lowProducts->first();
            $qty = (float) $lowProduct->total_qty;
            $unit = $lowProduct->product->unit->name ?? 'unit';

            $recommendations[] = [
                'type' => 'promo',
                'priority' => 'medium',
                'title' => 'Pertimbangkan Promo Produk Slow Moving',
                'message' => "Produk '{$lowProduct->product->name}' hanya terjual {$qty} {$unit}. Pertimbangkan promo atau bundling.",
            ];
        }

        // Rekomendasi berdasarkan jam ramai
        if (isset($hourlyAnalysis['peak_hour']['hour'])) {
            $peakHour = $hourlyAnalysis['peak_hour']['hour'];
            $recommendations[] = [
                'type' => 'strategy',
                'priority' => 'medium',
                'title' => 'Optimalkan Jam Ramai',
                'message' => "Jam {$peakHour}:00 adalah jam tersibuk dengan {$hourlyAnalysis['peak_hour']['transactions']} transaksi. Pastikan kesiapan staff dan stok di jam ini.",
            ];
        }

        // Rekomendasi berdasarkan perbandingan hari sebelumnya
        if ($previousDayComparison['available']) {
            if ($previousDayComparison['sales_percentage'] < -10) {
                $recommendations[] = [
                    'type' => 'alert',
                    'priority' => 'high',
                    'title' => 'Penjualan Menurun Signifikan',
                    'message' => "Penjualan turun {$previousDayComparison['sales_percentage']}% dari kemarin. Evaluasi strategi marketing atau cek feedback pelanggan.",
                ];
            } elseif ($previousDayComparison['sales_percentage'] > 10) {
                $recommendations[] = [
                    'type' => 'positive',
                    'priority' => 'low',
                    'title' => 'Penjualan Meningkat!',
                    'message' => "Penjualan naik {$previousDayComparison['sales_percentage']}% dari kemarin. Pertahankan momentum ini!",
                ];
            }
        }

        return $recommendations;
    }

    /**
     * Generate human-readable insight content
     */
    private function generateInsightContent(array $data): string
    {
        $content = [];

        // Summary
        $content[] = "Ringkasan Penjualan Hari Ini";
        $content[] = "Total Penjualan: Rp " . number_format($data['summary']['total_sales'], 0, ',', '.');
        $content[] = "Jumlah Transaksi: " . $data['summary']['total_transactions'] . " transaksi";
        $content[] = "Rata-rata per Transaksi: Rp " . number_format($data['summary']['average_transaction'], 0, ',', '.');

        if ($data['summary']['total_discount'] > 0) {
            $content[] = "Total Diskon: Rp " . number_format($data['summary']['total_discount'], 0, ',', '.');
        }

        // Top products
        if (!empty($data['top_products'])) {
            $content[] = "\nProduk Terlaris";
            foreach (array_slice($data['top_products'], 0, 3) as $index => $product) {
                $qty = (float) $product['quantity'];
                $unit = $product['unit'] ?? 'unit';
                $content[] = ($index + 1) . ". {$product['name']} - {$qty} {$unit} (Rp " . number_format($product['revenue'], 0, ',', '.') . ")";
            }
        }

        // Low selling products
        if (!empty($data['low_products'])) {
            $content[] = "\nProduk Perlu Perhatian";
            foreach (array_slice($data['low_products'], 0, 2) as $index => $product) {
                $qty = (float) $product['quantity'];
                $unit = $product['unit'] ?? 'unit';
                $content[] = "- {$product['name']} - hanya {$qty} {$unit}";
            }
        }

        // Hourly pattern
        if (isset($data['hourly_analysis']['peak_hour'])) {
            $content[] = "\nPola Jam Operasional";
            $content[] = "Jam Ramai: {$data['hourly_analysis']['peak_hour']['hour']}:00 ({$data['hourly_analysis']['peak_hour']['transactions']} transaksi)";
            $content[] = "Jam Sepi: {$data['hourly_analysis']['quiet_hour']['hour']}:00 ({$data['hourly_analysis']['quiet_hour']['transactions']} transaksi)";
        }

        // Previous day comparison
        if ($data['previous_day_comparison']['available']) {
            $comp = $data['previous_day_comparison'];
            $status = $comp['sales_percentage'] >= 0 ? 'naik' : 'turun';
            
            $content[] = "\nPerbandingan dengan Kemarin";
            $content[] = "Penjualan {$status} " . abs($comp['sales_percentage']) . "% (Rp " . number_format(abs($comp['sales_difference']), 0, ',', '.') . ")";
            $content[] = "Transaksi {$status} " . abs($comp['transaction_percentage']) . "% (" . abs($comp['transaction_difference']) . " transaksi)";
        }

        // Recommendations
        if (!empty($data['recommendations'])) {
            $content[] = "\nRekomendasi untuk Besok";
            foreach (array_slice($data['recommendations'], 0, 3) as $index => $rec) {
                $content[] = ($index + 1) . ". {$rec['message']}";
            }
        }

        return implode("\n", $content);
    }

    /**
     * Determine severity based on analysis
     */
    private function determineSeverity(array $data): string
    {
        // Critical jika penjualan turun drastis
        if ($data['previous_day_comparison']['available'] && $data['previous_day_comparison']['sales_percentage'] < -20) {
            return 'critical';
        }

        // Warning jika ada produk yang tidak laku
        if (!empty($data['low_products']) && count($data['low_products']) > 3) {
            return 'warning';
        }

        return 'info';
    }
}

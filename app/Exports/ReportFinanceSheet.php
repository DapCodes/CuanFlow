<?php

namespace App\Exports;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Purchase;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportFinanceSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithEvents
{
    protected Carbon $startDate;
    protected Carbon $endDate;

    public function __construct(Carbon $startDate, Carbon $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function title(): string
    {
        return 'Keuangan';
    }

    public function array(): array
    {
        $outletId = auth()->user()->outlet_id;

        // Sales data
        $sales = Sale::where('outlet_id', $outletId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('status', 'completed')
            ->get();

        $totalTax = $sales->sum('tax_amount');
        $totalDiscount = $sales->sum('discount_amount');
        $totalRevenue = $sales->sum('grand_total');

        // Sales by Category
        $salesByCategory = SaleItem::select('products.category_id', DB::raw('SUM(sale_items.subtotal) as total_revenue'), DB::raw('SUM(sale_items.quantity) as total_qty'))
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereHas('sale', function($q) use ($outletId) {
                $q->where('outlet_id', $outletId)
                  ->whereBetween('created_at', [$this->startDate, $this->endDate])
                  ->where('status', 'completed');
            })
            ->groupBy('products.category_id')
            ->get()
            ->map(function($item) {
                $category = Category::find($item->category_id);
                return [
                    'category_name' => $category ? $category->name : 'Tanpa Kategori',
                    'total_revenue' => $item->total_revenue,
                    'total_qty' => $item->total_qty,
                ];
            });

        // Refund Stats
        $refundedSales = Sale::where('outlet_id', $outletId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('status', 'refunded')
            ->get();
        
        $cancelledSales = Sale::where('outlet_id', $outletId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('status', 'cancelled')
            ->get();

        // Purchases Summary
        $purchases = Purchase::where('outlet_id', $outletId)
            ->whereBetween('purchase_date', [$this->startDate, $this->endDate])
            ->get();

        $totalPurchases = $purchases->sum('grand_total');
        $totalPurchasesPaid = $purchases->where('payment_status', 'paid')->sum('grand_total');
        $totalPurchasesUnpaid = $purchases->whereIn('payment_status', ['unpaid', 'partial'])->sum('grand_total');

        $data = [
            ['LAPORAN KEUANGAN'],
            ['Periode: ' . $this->startDate->format('d M Y') . ' - ' . $this->endDate->format('d M Y')],
            ['Tanggal Cetak: ' . now()->format('d M Y H:i')],
            [''],
            ['RINGKASAN PAJAK & DISKON'],
            [''],
            ['Metrik', 'Nilai (Rp)'],
            ['Total Pajak (PPN)', $totalTax],
            ['Total Diskon Diberikan', $totalDiscount],
            ['Pendapatan Bersih', $totalRevenue],
            [''],
            [''],
            ['PENJUALAN PER KATEGORI'],
            [''],
            ['Kategori', 'Qty Terjual', 'Pendapatan', '% Kontribusi'],
        ];

        foreach ($salesByCategory as $cat) {
            $data[] = [
                $cat['category_name'],
                $cat['total_qty'],
                $cat['total_revenue'],
                $totalRevenue > 0 ? number_format(($cat['total_revenue'] / $totalRevenue) * 100, 1) . '%' : '0%',
            ];
        }

        $data[] = [''];
        $data[] = [''];
        $data[] = ['REFUND & PEMBATALAN'];
        $data[] = [''];
        $data[] = ['Metrik', 'Jumlah', 'Nilai'];
        $data[] = ['Transaksi Refund', $refundedSales->count(), $refundedSales->sum('grand_total')];
        $data[] = ['Transaksi Dibatalkan', $cancelledSales->count(), 0];

        $data[] = [''];
        $data[] = [''];
        $data[] = ['RINGKASAN PEMBELIAN'];
        $data[] = [''];
        $data[] = ['Metrik', 'Nilai (Rp)'];
        $data[] = ['Total Pembelian', $totalPurchases];
        $data[] = ['Sudah Dibayar', $totalPurchasesPaid];
        $data[] = ['Belum Lunas (Hutang Dagang)', $totalPurchasesUnpaid];

        return $data;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            5 => ['font' => ['bold' => true, 'size' => 12]],
            13 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 20,
            'C' => 20,
            'D' => 15,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Format currency columns
                $sheet->getStyle('B:C')->getNumberFormat()->setFormatCode('#,##0');
            },
        ];
    }
}

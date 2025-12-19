<?php

namespace App\Exports;

use App\Models\Expense;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\RawMaterial;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title as ChartTitle;
use Illuminate\Support\Facades\DB;

class ReportSummarySheet implements FromArray, WithTitle, WithStyles, WithEvents
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
        return 'Ringkasan';
    }

    public function array(): array
    {
        $outletId = auth()->user()->outlet_id;

        $sales = Sale::where('outlet_id', $outletId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('status', 'completed')
            ->get();

        $expenses = Expense::where('outlet_id', $outletId)
            ->whereBetween('expense_date', [$this->startDate, $this->endDate])
            ->get();

        $totalRevenue = $sales->sum('grand_total');
        $totalTransactions = $sales->count();
        $totalExpenses = $expenses->sum('amount');
        
        $totalCogs = SaleItem::whereHas('sale', function($q) use ($outletId) {
                $q->where('outlet_id', $outletId)
                  ->whereBetween('created_at', [$this->startDate, $this->endDate])
                  ->where('status', 'completed');
            })
            ->sum(DB::raw('hpp * quantity'));

        $grossProfit = SaleItem::whereHas('sale', function($q) use ($outletId) {
                $q->where('outlet_id', $outletId)
                  ->whereBetween('created_at', [$this->startDate, $this->endDate])
                  ->where('status', 'completed');
            })
            ->sum('profit');

        $netProfit = $grossProfit - $totalExpenses;

        // Top Products
        $topProducts = SaleItem::select('product_name', DB::raw('SUM(quantity) as total_qty'))
            ->whereHas('sale', function($q) use ($outletId) {
                $q->where('outlet_id', $outletId)
                  ->whereBetween('created_at', [$this->startDate, $this->endDate])
                  ->where('status', 'completed');
            })
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        $data = [
            ['LAPORAN BISNIS KESELURUHAN'],
            ['Periode: ' . $this->startDate->format('d M Y') . ' - ' . $this->endDate->format('d M Y')],
            ['Tanggal Cetak: ' . now()->format('d M Y H:i')],
            [''],
            ['RINGKASAN KEUANGAN'],
            [''],
            ['Metrik', 'Nilai (Rp)'],
            ['Total Pendapatan', $totalRevenue],
            ['Total Transaksi', $totalTransactions . ' transaksi'],
            ['Total Pengeluaran', $totalExpenses],
            ['Total HPP (Estimasi)', $totalCogs],
            ['Laba Kotor', $grossProfit],
            ['Laba Bersih', $netProfit],
            [''],
            [''],
            ['TOP 5 PRODUK TERLARIS'],
            [''],
            ['Produk', 'Jumlah Terjual'],
        ];

        foreach ($topProducts as $product) {
            $data[] = [$product->product_name, $product->total_qty];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => '2563EB']], 
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
            2 => [
                'font' => ['italic' => true, 'size' => 11], 
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
            3 => [
                'font' => ['size' => 9, 'color' => ['rgb' => '6B7280']], 
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
            5 => ['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '059669']]],
            7 => [
                'font' => ['bold' => true, 'size' => 11], 
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'DBEAFE']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
            16 => ['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'DC2626']]],
            18 => [
                'font' => ['bold' => true, 'size' => 11], 
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FEF3C7']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Merge cells for title
                $sheet->mergeCells('A1:B1');
                $sheet->mergeCells('A2:B2');
                $sheet->mergeCells('A3:B3');
                
                // Column widths
                $sheet->getColumnDimension('A')->setWidth(35);
                $sheet->getColumnDimension('B')->setWidth(25);
                
                // Number formatting
                $sheet->getStyle('B8')->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('B10:B13')->getNumberFormat()->setFormatCode('#,##0');
                
                // Borders
                $sheet->getStyle('A7:B13')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D1D5DB']
                        ]
                    ]
                ]);
                
                // Top products borders
                $lastProductRow = 18 + min(5, $sheet->getHighestRow() - 18);
                $sheet->getStyle('A18:B' . $lastProductRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D1D5DB']
                        ]
                    ]
                ]);
                
                // Conditional coloring for profit/loss
                $netProfitCell = 'B13';
                $netProfitValue = $sheet->getCell($netProfitCell)->getValue();
                if (is_numeric($netProfitValue) && $netProfitValue < 0) {
                    $sheet->getStyle($netProfitCell)->getFont()->getColor()->setRGB('DC2626');
                } else {
                    $sheet->getStyle($netProfitCell)->getFont()->getColor()->setRGB('059669');
                }
            },
        ];
    }
}
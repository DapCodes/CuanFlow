<?php

namespace App\Exports;

use App\Models\Expense;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithCharts;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StatisticsExport implements WithMultipleSheets
{
    use Exportable;

    protected int $outletId;

    protected string $period;

    protected Carbon $startDate;

    protected Carbon $endDate;

    protected string $outletName;

    public function __construct(int $outletId, string $period, string $outletName)
    {
        $this->outletId = $outletId;
        $this->period = $period;
        $this->outletName = $outletName;
        [$this->startDate, $this->endDate] = $this->getDateRange($period);
    }

    private function getDateRange(string $period): array
    {
        return match ($period) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            '7' => [now()->subDays(6)->startOfDay(), now()->endOfDay()],
            '30' => [now()->subDays(29)->startOfDay(), now()->endOfDay()],
            'month' => [now()->startOfMonth(), now()->endOfDay()],
            'year' => [now()->startOfYear(), now()->endOfDay()],
            default => [now()->subDays(29)->startOfDay(), now()->endOfDay()],
        };
    }

    public function sheets(): array
    {
        return [
            new SummarySheet($this->outletId, $this->period, $this->startDate, $this->endDate, $this->outletName),
            new SalesTrendSheet($this->outletId, $this->startDate, $this->endDate),
            new TopProductsSheet($this->outletId, $this->startDate, $this->endDate),
            new PaymentMethodSheet($this->outletId, $this->startDate, $this->endDate),
            new HourlySalesSheet($this->outletId, $this->startDate, $this->endDate),
            new TransactionsSheet($this->outletId, $this->startDate, $this->endDate),
        ];
    }
}

// ========== SUMMARY SHEET (No Chart) ==========
class SummarySheet implements FromArray, WithEvents, WithStyles, WithTitle
{
    protected int $outletId;

    protected string $period;

    protected Carbon $startDate;

    protected Carbon $endDate;

    protected string $outletName;

    public function __construct(int $outletId, string $period, Carbon $startDate, Carbon $endDate, string $outletName)
    {
        $this->outletId = $outletId;
        $this->period = $period;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->outletName = $outletName;
    }

    public function title(): string
    {
        return 'Ringkasan';
    }

    public function array(): array
    {
        $periodLabel = match ($this->period) {
            'today' => 'Hari Ini ('.now()->format('d M Y').')',
            '7' => '7 Hari Terakhir',
            '30' => '30 Hari Terakhir',
            'month' => 'Bulan Ini ('.now()->format('F Y').')',
            'year' => 'Tahun Ini ('.now()->format('Y').')',
            default => '30 Hari Terakhir',
        };

        $sales = Sale::where('outlet_id', $this->outletId)
            ->completed()
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get();

        $totalRevenue = $sales->sum('grand_total');
        $totalTransactions = $sales->count();
        $grossProfit = $sales->sum(fn ($s) => $s->getTotalProfit());

        $totalExpenses = Expense::where('outlet_id', $this->outletId)
            ->whereBetween('expense_date', [$this->startDate->format('Y-m-d'), $this->endDate->format('Y-m-d')])
            ->where('amount', '>', 0)
            ->sum('amount');

        $netProfit = $totalRevenue - $totalExpenses;

        $totalProductsSold = SaleItem::whereHas('sale', function ($q) {
            $q->where('outlet_id', $this->outletId)
                ->completed()
                ->whereBetween('created_at', [$this->startDate, $this->endDate]);
        })->sum('quantity');

        $totalRefunds = Sale::where('outlet_id', $this->outletId)
            ->where('status', 'refunded')
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->sum('grand_total');

        $days = max(1, $this->startDate->diffInDays($this->endDate) + 1);
        $avgRevenuePerDay = $totalRevenue / $days;
        $avgTransactionsPerDay = round($totalTransactions / $days, 1);

        return [
            [''],
            ['LAPORAN DASHBOARD & STATISTIK'],
            ['Periode: '.$periodLabel],
            [$this->outletName],
            ['Tanggal Cetak: '.now()->format('d M Y H:i')],
            [''],
            [''],
            ['RINGKASAN KEUANGAN'],
            [''],
            ['Metrik', 'Nilai'],
            ['Total Pendapatan', 'Rp '.number_format($totalRevenue, 0, ',', '.')],
            ['Total Transaksi', number_format($totalTransactions)],
            ['Produk Terjual', number_format($totalProductsSold)],
            ['Laba Kotor', 'Rp '.number_format($grossProfit, 0, ',', '.')],
            ['Total Pengeluaran', 'Rp '.number_format($totalExpenses, 0, ',', '.')],
            ['Laba Bersih', 'Rp '.number_format($netProfit, 0, ',', '.')],
            ['Total Refund', 'Rp '.number_format($totalRefunds, 0, ',', '.')],
            [''],
            ['RATA-RATA HARIAN'],
            [''],
            ['Metrik', 'Nilai'],
            ['Pendapatan per Hari', 'Rp '.number_format($avgRevenuePerDay, 0, ',', '.')],
            ['Transaksi per Hari', $avgTransactionsPerDay],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2 => ['font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => '10B981']]],
            3 => ['font' => ['italic' => true, 'size' => 11, 'color' => ['rgb' => '6B7280']]],
            4 => ['font' => ['bold' => true, 'size' => 14]],
            5 => ['font' => ['size' => 10, 'color' => ['rgb' => '9CA3AF']]],
            8 => ['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '3B82F6']]],
            10 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E5E7EB']]],
            19 => ['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '8B5CF6']]],
            21 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E5E7EB']]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Set column widths for better readability
                $sheet->getColumnDimension('A')->setWidth(30);
                $sheet->getColumnDimension('B')->setWidth(35);

                // Merge and center title cells
                $sheet->mergeCells('A2:B2');
                $sheet->mergeCells('A3:B3');
                $sheet->mergeCells('A4:B4');
                $sheet->mergeCells('A5:B5');

                // Center align titles
                $sheet->getStyle('A2:B5')->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Add auto-filter on header row for first table
                $sheet->setAutoFilter('A10:B10');

                // Add borders to first table (Ringkasan Keuangan)
                $sheet->getStyle('A10:B17')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D1D5DB'],
                        ],
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '9CA3AF'],
                        ],
                    ],
                ]);

                // Add auto-filter on header row for second table
                $sheet->setAutoFilter('A21:B21');

                // Add borders to second table (Rata-Rata Harian)
                $sheet->getStyle('A21:B23')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D1D5DB'],
                        ],
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '9CA3AF'],
                        ],
                    ],
                ]);

                // Add alternating row colors for better readability
                for ($i = 11; $i <= 17; $i++) {
                    if ($i % 2 == 1) {
                        $sheet->getStyle('A'.$i.':B'.$i)->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F9FAFB']],
                        ]);
                    }
                }

                // Align values to right for better number readability
                $sheet->getStyle('B11:B17')->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);
                $sheet->getStyle('B22:B23')->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);
            },
        ];
    }
}

// ========== SALES TREND SHEET WITH LINE CHART ==========
class SalesTrendSheet implements FromArray, WithCharts, WithEvents, WithStyles, WithTitle
{
    protected int $outletId;

    protected Carbon $startDate;

    protected Carbon $endDate;

    protected int $dataRowCount = 0;

    public function __construct(int $outletId, Carbon $startDate, Carbon $endDate)
    {
        $this->outletId = $outletId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function title(): string
    {
        return 'Tren Penjualan';
    }

    public function array(): array
    {
        $sales = Sale::where('outlet_id', $this->outletId)
            ->completed()
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->selectRaw('DATE(created_at) as date, SUM(grand_total) as total, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $periodLabel = $this->startDate->format('d M Y').' - '.$this->endDate->format('d M Y');

        $data = [
            [''],
            ['TREN PENJUALAN HARIAN'],
            ['Periode: '.$periodLabel],
            [''],
            ['Tanggal', 'Pendapatan (Rp)', 'Jumlah Transaksi'],
        ];

        $currentDate = $this->startDate->copy();
        $totalRevenue = 0;
        $totalTransactions = 0;

        while ($currentDate <= $this->endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $revenue = isset($sales[$dateStr]) ? (int) $sales[$dateStr]->total : 0;
            $count = isset($sales[$dateStr]) ? (int) $sales[$dateStr]->count : 0;

            $data[] = [
                $currentDate->format('d M Y'),
                $revenue,
                $count,
            ];

            $totalRevenue += $revenue;
            $totalTransactions += $count;
            $currentDate->addDay();
            $this->dataRowCount++;
        }

        $data[] = [''];
        $data[] = ['TOTAL', $totalRevenue, $totalTransactions];

        return $data;
    }

    public function charts()
    {
        $lastDataRow = 5 + $this->dataRowCount;

        // Labels (dates)
        $labels = [new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_STRING,
            "'Tren Penjualan'!\$A\$6:\$A\$".$lastDataRow,
            null,
            $this->dataRowCount
        )];

        // Revenue data series
        $values = [new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_NUMBER,
            "'Tren Penjualan'!\$B\$6:\$B\$".$lastDataRow,
            null,
            $this->dataRowCount
        )];

        $series = new DataSeries(
            DataSeries::TYPE_LINECHART,
            DataSeries::GROUPING_STANDARD,
            range(0, count($values) - 1),
            [],
            $labels,
            $values
        );

        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_BOTTOM, null, false);
        $title = new Title('Grafik Tren Penjualan');

        $chart = new Chart('SalesTrendChart', $title, $legend, $plotArea);
        $chart->setTopLeftPosition('E2');
        $chart->setBottomRightPosition('L18');

        return $chart;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2 => ['font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '10B981']]],
            3 => ['font' => ['italic' => true, 'size' => 11, 'color' => ['rgb' => '6B7280']]],
            5 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D1FAE5']]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(18);
                $sheet->getColumnDimension('B')->setWidth(22);
                $sheet->getColumnDimension('C')->setWidth(20);

                // Center title
                $sheet->mergeCells('A2:C2');
                $sheet->mergeCells('A3:C3');
                $sheet->getStyle('A2:C3')->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // Add auto-filter on header
                $sheet->setAutoFilter('A5:C5');

                // Total row styling
                $sheet->getStyle('A'.$lastRow.':C'.$lastRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FEF3C7']],
                ]);

                // Add borders to entire table
                $sheet->getStyle('A5:C'.$lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D1D5DB'],
                        ],
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '9CA3AF'],
                        ],
                    ],
                ]);

                // Right align numbers
                $sheet->getStyle('B6:C'.$lastRow)->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);

                // Alternating row colors
                for ($i = 6; $i < $lastRow - 1; $i++) {
                    if ($i % 2 == 0) {
                        $sheet->getStyle('A'.$i.':C'.$i)->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F0FDF4']],
                        ]);
                    }
                }
            },
        ];
    }
}

// ========== TOP PRODUCTS SHEET WITH BAR CHART ==========
class TopProductsSheet implements FromArray, WithCharts, WithEvents, WithStyles, WithTitle
{
    protected int $outletId;

    protected Carbon $startDate;

    protected Carbon $endDate;

    protected int $productCount = 0;

    public function __construct(int $outletId, Carbon $startDate, Carbon $endDate)
    {
        $this->outletId = $outletId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function title(): string
    {
        return 'Produk Terlaris';
    }

    public function array(): array
    {
        $topProducts = SaleItem::whereHas('sale', function ($q) {
            $q->where('outlet_id', $this->outletId)
                ->completed()
                ->whereBetween('created_at', [$this->startDate, $this->endDate]);
        })
            ->selectRaw('product_name, SUM(quantity) as total_qty, SUM(subtotal) as total_revenue')
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        $periodLabel = $this->startDate->format('d M Y').' - '.$this->endDate->format('d M Y');

        $data = [
            [''],
            ['TOP 10 PRODUK TERLARIS'],
            ['Periode: '.$periodLabel],
            [''],
            ['No', 'Nama Produk', 'Qty Terjual', 'Revenue (Rp)'],
        ];

        $no = 1;
        foreach ($topProducts as $product) {
            $data[] = [
                $no++,
                $product->product_name,
                (int) $product->total_qty,
                (int) $product->total_revenue,
            ];
            $this->productCount++;
        }

        return $data;
    }

    public function charts()
    {
        if ($this->productCount === 0) {
            return null;
        }

        $lastDataRow = 5 + $this->productCount;

        $labels = [new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_STRING,
            "'Produk Terlaris'!\$B\$6:\$B\$".$lastDataRow,
            null,
            $this->productCount
        )];

        $values = [new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_NUMBER,
            "'Produk Terlaris'!\$C\$6:\$C\$".$lastDataRow,
            null,
            $this->productCount
        )];

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_STANDARD,
            range(0, count($values) - 1),
            [],
            $labels,
            $values
        );
        $series->setPlotDirection(DataSeries::DIRECTION_BAR);

        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_BOTTOM, null, false);
        $title = new Title('Qty Produk Terjual');

        $chart = new Chart('TopProductsChart', $title, $legend, $plotArea);
        $chart->setTopLeftPosition('F2');
        $chart->setBottomRightPosition('N18');

        return $chart;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2 => ['font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'F59E0B']]],
            3 => ['font' => ['italic' => true, 'size' => 11, 'color' => ['rgb' => '6B7280']]],
            5 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FEF3C7']]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(6);
                $sheet->getColumnDimension('B')->setWidth(35);
                $sheet->getColumnDimension('C')->setWidth(15);
                $sheet->getColumnDimension('D')->setWidth(20);

                // Center title
                $sheet->mergeCells('A2:D2');
                $sheet->mergeCells('A3:D3');
                $sheet->getStyle('A2:D3')->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // Add auto-filter on header
                $sheet->setAutoFilter('A5:D5');

                // Add borders to table
                $sheet->getStyle('A5:D'.$lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D1D5DB'],
                        ],
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '9CA3AF'],
                        ],
                    ],
                ]);

                // Center No column
                $sheet->getStyle('A5:A'.$lastRow)->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Right align numbers
                $sheet->getStyle('C6:D'.$lastRow)->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);

                // Highlight top 3 products
                for ($i = 6; $i <= min(8, $lastRow); $i++) {
                    $sheet->getStyle('A'.$i.':D'.$i)->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'ECFDF5']],
                    ]);
                }

                // Alternating row colors for remaining rows
                for ($i = 9; $i <= $lastRow; $i++) {
                    if ($i % 2 == 1) {
                        $sheet->getStyle('A'.$i.':D'.$i)->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFBEB']],
                        ]);
                    }
                }
            },
        ];
    }
}

// ========== PAYMENT METHOD SHEET WITH PIE CHART ==========
class PaymentMethodSheet implements FromArray, WithCharts, WithEvents, WithStyles, WithTitle
{
    protected int $outletId;

    protected Carbon $startDate;

    protected Carbon $endDate;

    protected int $methodCount = 0;

    public function __construct(int $outletId, Carbon $startDate, Carbon $endDate)
    {
        $this->outletId = $outletId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function title(): string
    {
        return 'Metode Pembayaran';
    }

    public function array(): array
    {
        $payments = Sale::where('outlet_id', $this->outletId)
            ->completed()
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->selectRaw('payment_method, SUM(grand_total) as total, COUNT(*) as count')
            ->groupBy('payment_method')
            ->get();

        $periodLabel = $this->startDate->format('d M Y').' - '.$this->endDate->format('d M Y');

        $data = [
            [''],
            ['DISTRIBUSI METODE PEMBAYARAN'],
            ['Periode: '.$periodLabel],
            [''],
            ['Metode', 'Jumlah Transaksi', 'Total Nilai (Rp)'],
        ];

        foreach ($payments as $payment) {
            $label = match ($payment->payment_method) {
                'cash' => 'Tunai',
                'qris' => 'QRIS',
                'transfer' => 'Transfer',
                'card' => 'Kartu',
                'debt' => 'Hutang',
                default => ucfirst($payment->payment_method),
            };

            $data[] = [
                $label,
                (int) $payment->count,
                (int) $payment->total,
            ];
            $this->methodCount++;
        }

        return $data;
    }

    public function charts()
    {
        if ($this->methodCount === 0) {
            return null;
        }

        $lastDataRow = 5 + $this->methodCount;

        $labels = [new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_STRING,
            "'Metode Pembayaran'!\$A\$6:\$A\$".$lastDataRow,
            null,
            $this->methodCount
        )];

        $values = [new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_NUMBER,
            "'Metode Pembayaran'!\$C\$6:\$C\$".$lastDataRow,
            null,
            $this->methodCount
        )];

        $series = new DataSeries(
            DataSeries::TYPE_PIECHART,
            null,
            range(0, count($values) - 1),
            [],
            $labels,
            $values
        );

        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);
        $title = new Title('Distribusi Pembayaran');

        $chart = new Chart('PaymentMethodChart', $title, $legend, $plotArea);
        $chart->setTopLeftPosition('E2');
        $chart->setBottomRightPosition('L16');

        return $chart;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2 => ['font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '3B82F6']]],
            3 => ['font' => ['italic' => true, 'size' => 11, 'color' => ['rgb' => '6B7280']]],
            5 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'DBEAFE']]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(20);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(22);

                // Center title
                $sheet->mergeCells('A2:C2');
                $sheet->mergeCells('A3:C3');
                $sheet->getStyle('A2:C3')->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // Add auto-filter on header
                $sheet->setAutoFilter('A5:C5');

                // Add borders to table
                $sheet->getStyle('A5:C'.$lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D1D5DB'],
                        ],
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '9CA3AF'],
                        ],
                    ],
                ]);

                // Right align numbers
                $sheet->getStyle('B6:C'.$lastRow)->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);

                // Alternating row colors
                for ($i = 6; $i <= $lastRow; $i++) {
                    if ($i % 2 == 0) {
                        $sheet->getStyle('A'.$i.':C'.$i)->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'EFF6FF']],
                        ]);
                    }
                }
            },
        ];
    }
}

// ========== HOURLY SALES SHEET WITH BAR CHART ==========
class HourlySalesSheet implements FromArray, WithCharts, WithEvents, WithStyles, WithTitle
{
    protected int $outletId;

    protected Carbon $startDate;

    protected Carbon $endDate;

    public function __construct(int $outletId, Carbon $startDate, Carbon $endDate)
    {
        $this->outletId = $outletId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function title(): string
    {
        return 'Penjualan per Jam';
    }

    public function array(): array
    {
        $hourlyData = Sale::where('outlet_id', $this->outletId)
            ->completed()
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count, SUM(grand_total) as total')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->keyBy('hour');

        $periodLabel = $this->startDate->format('d M Y').' - '.$this->endDate->format('d M Y');

        $data = [
            [''],
            ['ANALISIS PENJUALAN PER JAM (PEAK HOURS)'],
            ['Periode: '.$periodLabel],
            [''],
            ['Jam', 'Jumlah Transaksi', 'Total Pendapatan (Rp)'],
        ];

        $maxCount = 0;
        $peakHour = 0;

        for ($h = 6; $h <= 22; $h++) {
            $count = isset($hourlyData[$h]) ? (int) $hourlyData[$h]->count : 0;
            $total = isset($hourlyData[$h]) ? (int) $hourlyData[$h]->total : 0;

            if ($count > $maxCount) {
                $maxCount = $count;
                $peakHour = $h;
            }

            $data[] = [
                sprintf('%02d:00 - %02d:00', $h, $h + 1),
                $count,
                $total,
            ];
        }

        $data[] = [''];
        $data[] = ['Jam Tersibuk:', sprintf('%02d:00 - %02d:00', $peakHour, $peakHour + 1), $maxCount.' transaksi'];

        return $data;
    }

    public function charts()
    {
        // 17 jam (06:00 - 22:00) - adjusted for new row structure
        $labels = [new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_STRING,
            "'Penjualan per Jam'!\$A\$6:\$A\$22",
            null,
            17
        )];

        $values = [new DataSeriesValues(
            DataSeriesValues::DATASERIES_TYPE_NUMBER,
            "'Penjualan per Jam'!\$B\$6:\$B\$22",
            null,
            17
        )];

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_STANDARD,
            range(0, count($values) - 1),
            [],
            $labels,
            $values
        );
        $series->setPlotDirection(DataSeries::DIRECTION_COL);

        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_BOTTOM, null, false);
        $title = new Title('Transaksi per Jam');

        $chart = new Chart('HourlyChart', $title, $legend, $plotArea);
        $chart->setTopLeftPosition('E2');
        $chart->setBottomRightPosition('N18');

        return $chart;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2 => ['font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '06B6D4']]],
            3 => ['font' => ['italic' => true, 'size' => 11, 'color' => ['rgb' => '6B7280']]],
            5 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'CFFAFE']]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // Set column widths
                $sheet->getColumnDimension('A')->setWidth(18);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(25);

                // Center title
                $sheet->mergeCells('A2:C2');
                $sheet->mergeCells('A3:C3');
                $sheet->getStyle('A2:C3')->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // Add auto-filter on header
                $sheet->setAutoFilter('A5:C5');

                // Peak hour row styling
                $sheet->getStyle('A'.$lastRow.':C'.$lastRow)->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '0891B2']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'ECFEFF']],
                ]);

                // Add borders to data table (row 5 to 22 = header + 17 hours)
                $sheet->getStyle('A5:C22')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D1D5DB'],
                        ],
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '9CA3AF'],
                        ],
                    ],
                ]);

                // Right align numbers
                $sheet->getStyle('B6:C22')->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);

                // Alternating row colors
                for ($i = 6; $i <= 22; $i++) {
                    if ($i % 2 == 0) {
                        $sheet->getStyle('A'.$i.':C'.$i)->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'ECFEFF']],
                        ]);
                    }
                }
            },
        ];
    }
}

// ========== TRANSACTIONS SHEET (No Chart) ==========
class TransactionsSheet implements FromArray, WithEvents, WithStyles, WithTitle
{
    protected int $outletId;

    protected Carbon $startDate;

    protected Carbon $endDate;

    public function __construct(int $outletId, Carbon $startDate, Carbon $endDate)
    {
        $this->outletId = $outletId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function title(): string
    {
        return 'Daftar Transaksi';
    }

    public function array(): array
    {
        $sales = Sale::where('outlet_id', $this->outletId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->with('cashier')
            ->orderByDesc('created_at')
            ->get();

        $periodLabel = $this->startDate->format('d M Y').' - '.$this->endDate->format('d M Y');

        $data = [
            [''],
            ['DAFTAR SELURUH TRANSAKSI'],
            ['Periode: '.$periodLabel],
            [''],
            ['No', 'Invoice', 'Tanggal', 'Waktu', 'Kasir', 'Metode', 'Subtotal', 'Diskon', 'Total', 'Status'],
        ];

        $no = 1;
        foreach ($sales as $sale) {
            $methodLabel = match ($sale->payment_method) {
                'cash' => 'Tunai',
                'qris' => 'QRIS',
                'transfer' => 'Transfer',
                'card' => 'Kartu',
                'debt' => 'Hutang',
                default => ucfirst($sale->payment_method ?? ''),
            };

            $statusLabel = match ($sale->status) {
                'completed' => 'Selesai',
                'refunded' => 'Refund',
                'cancelled' => 'Batal',
                default => ucfirst($sale->status ?? ''),
            };

            $data[] = [
                $no++,
                $sale->invoice_number,
                $sale->created_at->format('d M Y'),
                $sale->created_at->format('H:i'),
                $sale->cashier->name ?? '-',
                $methodLabel,
                (int) $sale->subtotal,
                (int) $sale->discount_amount,
                (int) $sale->grand_total,
                $statusLabel,
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            2 => ['font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'EC4899']]],
            3 => ['font' => ['italic' => true, 'size' => 11, 'color' => ['rgb' => '6B7280']]],
            5 => ['font' => ['bold' => true, 'size' => 10], 'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FCE7F3']]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // Set column widths for better readability
                $sheet->getColumnDimension('A')->setWidth(6);   // No
                $sheet->getColumnDimension('B')->setWidth(20);  // Invoice
                $sheet->getColumnDimension('C')->setWidth(14);  // Tanggal
                $sheet->getColumnDimension('D')->setWidth(8);   // Waktu
                $sheet->getColumnDimension('E')->setWidth(18);  // Kasir
                $sheet->getColumnDimension('F')->setWidth(12);  // Metode
                $sheet->getColumnDimension('G')->setWidth(15);  // Subtotal
                $sheet->getColumnDimension('H')->setWidth(12);  // Diskon
                $sheet->getColumnDimension('I')->setWidth(15);  // Total
                $sheet->getColumnDimension('J')->setWidth(12);  // Status

                // Center title
                $sheet->mergeCells('A2:J2');
                $sheet->mergeCells('A3:J3');
                $sheet->getStyle('A2:J3')->applyFromArray([
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // Add auto-filter on header
                $sheet->setAutoFilter('A5:J5');

                // Add borders to entire table
                $sheet->getStyle('A5:J'.$lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D1D5DB'],
                        ],
                        'outline' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '9CA3AF'],
                        ],
                    ],
                ]);

                // Center align specific columns
                $sheet->getStyle('A5:A'.$lastRow)->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle('D5:D'.$lastRow)->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle('J5:J'.$lastRow)->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Right align number columns
                $sheet->getStyle('G6:I'.$lastRow)->applyFromArray([
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);

                // Alternating row colors for better readability
                for ($i = 6; $i <= $lastRow; $i++) {
                    if ($i % 2 == 0) {
                        $sheet->getStyle('A'.$i.':J'.$i)->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FDF2F8']],
                        ]);
                    }
                }
            },
        ];
    }
}

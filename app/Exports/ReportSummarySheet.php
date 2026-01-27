<?php

namespace App\Exports;

use App\Models\Expense;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportSummarySheet implements FromArray, WithEvents, WithStyles, WithTitle
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
        $totalTax = $sales->sum('tax_amount');
        $totalDiscount = $sales->sum('discount_amount');
        $totalSubtotal = $sales->sum('subtotal');

        $totalCogs = SaleItem::whereHas('sale', function ($q) use ($outletId) {
            $q->where('outlet_id', $outletId)
                ->whereBetween('created_at', [$this->startDate, $this->endDate])
                ->where('status', 'completed');
        })
            ->sum(DB::raw('hpp * quantity'));

        $grossProfit = ($totalSubtotal - $totalDiscount) - $totalCogs;

        $extraIncome = abs($expenses->where('amount', '<', 0)->sum('amount'));
        $totalExpensesOnly = $expenses->where('amount', '>', 0)->sum('amount');

        $netProfit = $grossProfit + $extraIncome - $totalExpensesOnly;

        // Top Products
        $topProducts = SaleItem::select('product_name', DB::raw('SUM(quantity) as total_qty'))
            ->whereHas('sale', function ($q) use ($outletId) {
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
            ['Periode: '.$this->startDate->format('d M Y').' - '.$this->endDate->format('d M Y')],
            ['Tanggal Cetak: '.now()->format('d M Y H:i')],
            [''],
            ['RINGKASAN KEUANGAN'],
            [''],
            ['Metrik', 'Nilai'],
            ['Total Pendapatan', $totalRevenue],
            ['Total Transaksi', $totalTransactions],
            ['Total Pengeluaran Operasional', $totalExpensesOnly],
            ['Total Pemasukan Lain', $extraIncome],
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
            // Header utama - Background kuning
            1 => [
                'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => '000000']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FCD34D']], // Kuning
            ],
            // Sub header periode - Background abu muda
            2 => [
                'font' => ['italic' => true, 'size' => 11, 'color' => ['rgb' => '374151']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F3F4F6']], // Abu muda
            ],
            // Tanggal cetak - Background abu muda
            3 => [
                'font' => ['size' => 9, 'color' => ['rgb' => '6B7280']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F3F4F6']], // Abu muda
            ],
            // Section title - Background abu sedang
            5 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '000000']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D1D5DB']], // Abu sedang
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ],
            // Header tabel - Background kuning
            7 => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '000000']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FDE68A']], // Kuning muda
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            17 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '000000']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D1D5DB']], // Abu sedang
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ],
            19 => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '000000']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FDE68A']], // Kuning muda
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                // Merge cells for title
                $sheet->mergeCells('A1:B1');
                $sheet->mergeCells('A2:B2');
                $sheet->mergeCells('A3:B3');
                $sheet->mergeCells('A5:B5');
                $sheet->mergeCells('A17:B17');

                // Row heights
                $sheet->getRowDimension(1)->setRowHeight(35);
                $sheet->getRowDimension(2)->setRowHeight(22);
                $sheet->getRowDimension(3)->setRowHeight(20);
                $sheet->getRowDimension(4)->setRowHeight(10); // Spacing
                $sheet->getRowDimension(5)->setRowHeight(25);
                $sheet->getRowDimension(6)->setRowHeight(10); // Spacing
                $sheet->getRowDimension(7)->setRowHeight(25);

                // Column widths
                $sheet->getColumnDimension('A')->setWidth(38);
                $sheet->getColumnDimension('B')->setWidth(28);

                // Border untuk header
                $sheet->getStyle('A1:B1')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']],
                    ],
                ]);

                $sheet->getStyle('A2:B3')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9CA3AF']],
                    ],
                ]);

                $sheet->getStyle('A5:B5')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']],
                    ],
                ]);

                // Ringkasan Keuangan
                $sheet->getStyle('A7:B7')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']],
                    ],
                ]);

                // Set row heights untuk ringkasan
                for ($i = 8; $i <= 14; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(22);
                }

                $sheet->getStyle('A8:B14')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9CA3AF']],
                    ],
                ]);

                // Alternating colors dan alignment untuk ringkasan
                for ($i = 8; $i <= 14; $i++) {
                    if ($i % 2 == 0) {
                        $bgColor = 'FFFFFF';
                    } else {
                        $bgColor = 'F9FAFB';
                    }
                    $sheet->getStyle('A'.$i.':B'.$i)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB($bgColor);

                    $sheet->getStyle('A'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle('B'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A'.$i.':B'.$i)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                }

                // Number formatting
                $sheet->getStyle('B8')->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('B9')->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('B10:B14')->getNumberFormat()->setFormatCode('#,##0');

                // Conditional coloring untuk Laba Bersih
                $netProfitCell = 'B14';
                $netProfitValue = $sheet->getCell($netProfitCell)->getValue();
                if (is_numeric($netProfitValue) && $netProfitValue < 0) {
                    $sheet->getStyle($netProfitCell)->getFont()->getColor()->setRGB('DC2626');
                } else {
                    $sheet->getStyle($netProfitCell)->getFont()->getColor()->setRGB('059669');
                }
                $sheet->getStyle('A14:B14')->getFont()->setBold(true);

                // Top Products Section
                $sheet->getRowDimension(15)->setRowHeight(10);
                $sheet->getRowDimension(16)->setRowHeight(10);
                $sheet->getRowDimension(17)->setRowHeight(25);
                $sheet->getRowDimension(18)->setRowHeight(10);
                $sheet->getRowDimension(19)->setRowHeight(25);

                $sheet->getStyle('A17:B17')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']],
                    ],
                ]);

                $sheet->getStyle('A19:B19')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']],
                    ],
                ]);

                // Top products data
                for ($i = 20; $i <= $lastRow; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(22);
                }

                $sheet->getStyle('A20:B'.$lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9CA3AF']],
                    ],
                ]);

                // Alternating colors untuk top products
                for ($i = 20; $i <= $lastRow; $i++) {
                    if ($i % 2 == 0) {
                        $bgColor = 'FFFFFF';
                    } else {
                        $bgColor = 'F9FAFB';
                    }
                    $sheet->getStyle('A'.$i.':B'.$i)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB($bgColor);

                    $sheet->getStyle('A'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle('B'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'.$i.':B'.$i)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                }

                $sheet->getStyle('B20:B'.$lastRow)->getNumberFormat()->setFormatCode('#,##0');

                // Highlight produk #1
                if ($lastRow >= 20) {
                    $sheet->getStyle('A20:B20')->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB('FEF08A'); // Kuning highlight
                    $sheet->getStyle('A20:B20')->getFont()->setBold(true);

                    $sheet->getStyle('A20:B20')->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => 'EAB308']],
                        ],
                    ]);
                }
            },
        ];
    }
}

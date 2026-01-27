<?php

namespace App\Exports;

use App\Models\Category;
use App\Models\Purchase;
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

class ReportFinanceSheet implements FromArray, WithEvents, WithStyles, WithTitle
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
        $salesByCategory = SaleItem::select(
            'categories.name as category_name',
            DB::raw('SUM(sale_items.subtotal) as total_revenue'),
            DB::raw('SUM(sale_items.quantity) as total_qty')
        )
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->leftJoin('categories', 'products.category_id', '=', 'categories.id')
            ->whereHas('sale', function ($q) use ($outletId) {
                $q->where('outlet_id', $outletId)
                    ->whereBetween('created_at', [$this->startDate, $this->endDate])
                    ->where('status', 'completed');
            })
            ->groupBy('categories.id', 'categories.name')
            ->get()
            ->map(function ($item) {
                return [
                    'category_name' => $item->category_name ?? 'Tanpa Kategori',
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
            ['Periode: '.$this->startDate->format('d M Y').' - '.$this->endDate->format('d M Y')],
            ['Tanggal Cetak: '.now()->format('d M Y H:i')],
            [''],
            ['RINGKASAN PAJAK & DISKON'],
            [''],
            ['Metrik', 'Nilai (Rp)'],
            ['Total Pajak (PPN)', $totalTax],
            ['Total Diskon Diberikan', $totalDiscount],
            ['Penjualan Bersih (Sebelum Pajak)', $sales->sum('subtotal') - $totalDiscount],
            ['Total Penerimaan (Omzet)', $totalRevenue],
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
                $totalRevenue > 0 ? number_format(($cat['total_revenue'] / $totalRevenue) * 100, 1).'%' : '0%',
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
            // Header utama - Background kuning
            1 => [
                'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => '000000']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FCD34D']],
            ],
            // Sub header periode - Background abu muda
            2 => [
                'font' => ['italic' => true, 'size' => 11, 'color' => ['rgb' => '374151']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F3F4F6']],
            ],
            // Tanggal cetak - Background abu muda
            3 => [
                'font' => ['size' => 9, 'color' => ['rgb' => '6B7280']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F3F4F6']],
            ],
            // Section title - Background abu sedang
            5 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '000000']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D1D5DB']],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ],
            // Header tabel - Background kuning
            7 => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '000000']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FDE68A']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            14 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '000000']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D1D5DB']],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ],
            16 => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '000000']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FDE68A']],
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
                $sheet->mergeCells('A1:D1');
                $sheet->mergeCells('A2:D2');
                $sheet->mergeCells('A3:D3');
                $sheet->mergeCells('A5:D5');
                $sheet->mergeCells('A14:D14');

                // Row heights
                $sheet->getRowDimension(1)->setRowHeight(35);
                $sheet->getRowDimension(2)->setRowHeight(22);
                $sheet->getRowDimension(3)->setRowHeight(20);
                $sheet->getRowDimension(4)->setRowHeight(10);
                $sheet->getRowDimension(5)->setRowHeight(25);
                $sheet->getRowDimension(6)->setRowHeight(10);
                $sheet->getRowDimension(7)->setRowHeight(25);

                // Column widths
                $sheet->getColumnDimension('A')->setWidth(38);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(18);

                // Border untuk header
                $sheet->getStyle('A1:D1')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']],
                    ],
                ]);

                $sheet->getStyle('A2:D3')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9CA3AF']],
                    ],
                ]);

                $sheet->getStyle('A5:D5')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']],
                    ],
                ]);

                // Section 1: Pajak & Diskon
                $sheet->getStyle('A7:B7')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']],
                    ],
                ]);

                for ($i = 8; $i <= 11; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(22);
                }

                $sheet->getStyle('A8:B11')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9CA3AF']],
                    ],
                ]);

                for ($i = 8; $i <= 11; $i++) {
                    $bgColor = ($i % 2 == 0) ? 'FFFFFF' : 'F9FAFB';
                    $sheet->getStyle('A'.$i.':B'.$i)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB($bgColor);
                    $sheet->getStyle('A'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle('B'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A'.$i.':B'.$i)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                }

                $sheet->getStyle('B8:B11')->getNumberFormat()->setFormatCode('#,##0');

                // Section 2: Penjualan per Kategori
                $sheet->getRowDimension(12)->setRowHeight(10);
                $sheet->getRowDimension(13)->setRowHeight(10);
                $sheet->getRowDimension(14)->setRowHeight(25);
                $sheet->getRowDimension(15)->setRowHeight(10);
                $sheet->getRowDimension(16)->setRowHeight(25);

                $sheet->getStyle('A14:D14')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']],
                    ],
                ]);

                $sheet->getStyle('A16:D16')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']],
                    ],
                ]);

                // Find refund section
                $refundSectionRow = 0;
                for ($i = 17; $i <= $lastRow; $i++) {
                    if ($sheet->getCell('A'.$i)->getValue() === 'REFUND & PEMBATALAN') {
                        $refundSectionRow = $i;
                        break;
                    }
                }

                $categoryEndRow = $refundSectionRow > 0 ? $refundSectionRow - 3 : $lastRow;

                for ($i = 17; $i <= $categoryEndRow; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(22);
                }

                $sheet->getStyle('A17:D'.$categoryEndRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9CA3AF']],
                    ],
                ]);

                for ($i = 17; $i <= $categoryEndRow; $i++) {
                    $bgColor = ($i % 2 == 0) ? 'FFFFFF' : 'F9FAFB';
                    $sheet->getStyle('A'.$i.':D'.$i)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB($bgColor);
                    $sheet->getStyle('A'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle('B'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('C'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('D'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'.$i.':D'.$i)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                }

                $sheet->getStyle('B17:B'.$categoryEndRow)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('C17:C'.$categoryEndRow)->getNumberFormat()->setFormatCode('#,##0');

                // Section 3: Refund & Pembatalan
                if ($refundSectionRow > 0) {
                    $refundHeaderRow = $refundSectionRow + 2;

                    $sheet->getRowDimension($refundSectionRow)->setRowHeight(25);
                    $sheet->getRowDimension($refundSectionRow + 1)->setRowHeight(10);
                    $sheet->getRowDimension($refundHeaderRow)->setRowHeight(25);

                    $sheet->mergeCells('A'.$refundSectionRow.':D'.$refundSectionRow);

                    $sheet->getStyle('A'.$refundSectionRow.':D'.$refundSectionRow)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '000000']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D1D5DB']],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']],
                        ],
                    ]);

                    $sheet->getStyle('A'.$refundHeaderRow.':C'.$refundHeaderRow)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '000000']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FDE68A']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']],
                        ],
                    ]);

                    // Find purchase section
                    $purchaseSectionRow = 0;
                    for ($i = $refundHeaderRow + 1; $i <= $lastRow; $i++) {
                        if ($sheet->getCell('A'.$i)->getValue() === 'RINGKASAN PEMBELIAN') {
                            $purchaseSectionRow = $i;
                            break;
                        }
                    }

                    $refundEndRow = $purchaseSectionRow > 0 ? $purchaseSectionRow - 3 : $lastRow;

                    for ($i = $refundHeaderRow + 1; $i <= $refundEndRow; $i++) {
                        $sheet->getRowDimension($i)->setRowHeight(22);
                    }

                    $sheet->getStyle('A'.($refundHeaderRow + 1).':C'.$refundEndRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9CA3AF']],
                        ],
                    ]);

                    for ($i = $refundHeaderRow + 1; $i <= $refundEndRow; $i++) {
                        $bgColor = ($i % 2 == 0) ? 'FFFFFF' : 'F9FAFB';
                        $sheet->getStyle('A'.$i.':C'.$i)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB($bgColor);
                        $sheet->getStyle('A'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        $sheet->getStyle('B'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('C'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        $sheet->getStyle('A'.$i.':C'.$i)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    }

                    $sheet->getStyle('B'.($refundHeaderRow + 1).':B'.$refundEndRow)->getNumberFormat()->setFormatCode('#,##0');
                    $sheet->getStyle('C'.($refundHeaderRow + 1).':C'.$refundEndRow)->getNumberFormat()->setFormatCode('#,##0');

                    // Section 4: Ringkasan Pembelian
                    if ($purchaseSectionRow > 0) {
                        $purchaseHeaderRow = $purchaseSectionRow + 2;

                        $sheet->getRowDimension($purchaseSectionRow)->setRowHeight(25);
                        $sheet->getRowDimension($purchaseSectionRow + 1)->setRowHeight(10);
                        $sheet->getRowDimension($purchaseHeaderRow)->setRowHeight(25);

                        $sheet->mergeCells('A'.$purchaseSectionRow.':D'.$purchaseSectionRow);

                        $sheet->getStyle('A'.$purchaseSectionRow.':D'.$purchaseSectionRow)->applyFromArray([
                            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '000000']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D1D5DB']],
                            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                            'borders' => [
                                'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']],
                            ],
                        ]);

                        $sheet->getStyle('A'.$purchaseHeaderRow.':B'.$purchaseHeaderRow)->applyFromArray([
                            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '000000']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FDE68A']],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                            'borders' => [
                                'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']],
                            ],
                        ]);

                        for ($i = $purchaseHeaderRow + 1; $i <= $lastRow; $i++) {
                            $sheet->getRowDimension($i)->setRowHeight(22);
                        }

                        $sheet->getStyle('A'.($purchaseHeaderRow + 1).':B'.$lastRow)->applyFromArray([
                            'borders' => [
                                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9CA3AF']],
                            ],
                        ]);

                        for ($i = $purchaseHeaderRow + 1; $i <= $lastRow; $i++) {
                            $bgColor = ($i % 2 == 0) ? 'FFFFFF' : 'F9FAFB';
                            $sheet->getStyle('A'.$i.':B'.$i)->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setRGB($bgColor);
                            $sheet->getStyle('A'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                            $sheet->getStyle('B'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                            $sheet->getStyle('A'.$i.':B'.$i)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                        }

                        $sheet->getStyle('B'.($purchaseHeaderRow + 1).':B'.$lastRow)->getNumberFormat()->setFormatCode('#,##0');
                    }
                }
            },
        ];
    }
}

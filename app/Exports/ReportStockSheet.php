<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\RawMaterial;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportStockSheet implements FromArray, WithEvents, WithStyles, WithTitle
{
    public function title(): string
    {
        return 'Laporan Stok';
    }

    public function array(): array
    {
        $outletId = auth()->user()->outlet_id;

        $products = Product::where('outlet_id', $outletId)->with(['stocks', 'category', 'unit'])->get();
        $ingredients = RawMaterial::where('outlet_id', $outletId)->with(['stocks', 'category', 'unit'])->get();

        $data = [
            ['LAPORAN PERSEDIAAN PRODUK & BAHAN'],
            ['Tanggal Cetak: '.now()->format('d M Y H:i')],
            [''],
            ['STOK PRODUK JADI'],
            [''],
            ['Nama Produk', 'Kategori', 'Stok Saat Ini', 'Satuan', 'Min. Stok', 'Status'],
        ];

        foreach ($products as $product) {
            $currentStock = $product->stocks->sum('quantity');
            $status = $currentStock <= $product->min_stock ? 'RENDAH' : 'AMAN';
            $data[] = [
                $product->name,
                $product->category->name ?? '-',
                $currentStock,
                $product->unit->name ?? '',
                $product->min_stock,
                $status,
            ];
        }

        $data[] = [''];
        $data[] = [''];
        $data[] = ['STOK BAHAN BAKU'];
        $data[] = [''];
        $data[] = ['Nama Bahan', 'Kategori', 'Stok Saat Ini', 'Satuan', 'Min. Stok', 'Status'];

        foreach ($ingredients as $ingredient) {
            $currentStock = $ingredient->stocks->sum('quantity');
            $status = $currentStock <= $ingredient->min_stock ? 'RENDAH' : 'AMAN';
            $data[] = [
                $ingredient->name,
                $ingredient->category->name ?? '-',
                $currentStock,
                $ingredient->unit->name ?? '',
                $ingredient->min_stock,
                $status,
            ];
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
            // Tanggal cetak - Background abu muda
            2 => [
                'font' => ['size' => 9, 'color' => ['rgb' => '6B7280']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'F3F4F6']], // Abu muda
            ],
            // Section title - Background abu sedang
            4 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '000000']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D1D5DB']], // Abu sedang
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ],
            // Header tabel - Background kuning
            6 => [
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

                // Merge title
                $sheet->mergeCells('A1:F1');
                $sheet->mergeCells('A2:F2');

                // Row heights
                $sheet->getRowDimension(1)->setRowHeight(35);
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getRowDimension(3)->setRowHeight(10); // Spacing
                $sheet->getRowDimension(4)->setRowHeight(25);
                $sheet->getRowDimension(5)->setRowHeight(10); // Spacing
                $sheet->getRowDimension(6)->setRowHeight(25);

                // Column widths
                $sheet->getColumnDimension('A')->setWidth(32);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(18);
                $sheet->getColumnDimension('D')->setWidth(12);
                $sheet->getColumnDimension('E')->setWidth(15);
                $sheet->getColumnDimension('F')->setWidth(15);

                // Find ingredient section
                $ingredientHeaderRow = 0;
                $ingredientSectionRow = 0;
                for ($i = 1; $i <= $lastRow; $i++) {
                    if ($sheet->getCell('A'.$i)->getValue() === 'STOK BAHAN BAKU') {
                        $ingredientSectionRow = $i;
                        $ingredientHeaderRow = $i + 2;
                        break;
                    }
                }

                // Border untuk header
                $sheet->getStyle('A1:F1')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']],
                    ],
                ]);

                $sheet->getStyle('A2:F2')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9CA3AF']],
                    ],
                ]);

                $sheet->getStyle('A4:F4')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']],
                    ],
                ]);

                // Merge section title
                $sheet->mergeCells('A4:F4');

                // Border untuk header tabel produk
                $sheet->getStyle('A6:F6')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']],
                    ],
                ]);

                // Data produk
                $productEndRow = $ingredientSectionRow > 0 ? $ingredientSectionRow - 3 : $lastRow;

                // Set row heights untuk data produk
                for ($i = 7; $i <= $productEndRow; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(22);
                }

                // Border untuk data produk
                $sheet->getStyle('A7:F'.$productEndRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9CA3AF']],
                    ],
                ]);

                // Alternating colors untuk produk
                for ($i = 7; $i <= $productEndRow; $i++) {
                    $bgColor = ($i % 2 == 0) ? 'FFFFFF' : 'F9FAFB';
                    $sheet->getStyle('A'.$i.':F'.$i)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB($bgColor);

                    // Alignment
                    $sheet->getStyle('A'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle('B'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle('C'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('D'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('E'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('F'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('A'.$i.':F'.$i)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                    // Conditional formatting untuk status
                    $status = $sheet->getCell('F'.$i)->getValue();
                    if ($status === 'RENDAH') {
                        $sheet->getStyle('F'.$i)->getFont()->getColor()->setRGB('DC2626');
                        $sheet->getStyle('F'.$i)->getFont()->setBold(true);
                    }
                }

                // Style ingredient section
                if ($ingredientHeaderRow > 0) {
                    $sheet->getRowDimension($ingredientSectionRow)->setRowHeight(25);
                    $sheet->getRowDimension($ingredientSectionRow + 1)->setRowHeight(10);
                    $sheet->getRowDimension($ingredientHeaderRow)->setRowHeight(25);

                    $sheet->mergeCells('A'.$ingredientSectionRow.':F'.$ingredientSectionRow);

                    $sheet->getStyle('A'.$ingredientSectionRow.':F'.$ingredientSectionRow)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '000000']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D1D5DB']],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']],
                        ],
                    ]);

                    $sheet->getStyle('A'.$ingredientHeaderRow.':F'.$ingredientHeaderRow)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '000000']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FDE68A']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']],
                        ],
                    ]);

                    // Border dan alternating untuk data ingredient
                    $sheet->getStyle('A'.($ingredientHeaderRow + 1).':F'.$lastRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9CA3AF']],
                        ],
                    ]);

                    // Set row heights dan alternating colors untuk ingredients
                    for ($i = $ingredientHeaderRow + 1; $i <= $lastRow; $i++) {
                        $sheet->getRowDimension($i)->setRowHeight(22);

                        $bgColor = ($i % 2 == 0) ? 'FFFFFF' : 'F9FAFB';
                        $sheet->getStyle('A'.$i.':F'.$i)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB($bgColor);

                        // Alignment
                        $sheet->getStyle('A'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        $sheet->getStyle('B'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        $sheet->getStyle('C'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('D'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('E'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('F'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('A'.$i.':F'.$i)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                        // Conditional formatting untuk status
                        $status = $sheet->getCell('F'.$i)->getValue();
                        if ($status === 'RENDAH') {
                            $sheet->getStyle('F'.$i)->getFont()->getColor()->setRGB('DC2626');
                            $sheet->getStyle('F'.$i)->getFont()->setBold(true);
                        }
                    }
                }
            },
        ];
    }
}

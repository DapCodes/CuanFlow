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

class ReportStockSheet implements FromArray, WithTitle, WithStyles, WithEvents
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
            ['LAPORAN STOK PRODUK & BAHAN'],
            ['Tanggal Cetak: ' . now()->format('d M Y H:i')],
            [''],
            ['STOK PRODUK'],
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
                $status
            ];
        }

        $data[] = [''];
        $data[] = [''];
        $data[] = ['STOK BAHAN BAKU'];
        $headerRow = count($data) + 1;
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
                $status
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '2563EB']], 
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
            4 => ['font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '059669']]],
            5 => [
                'font' => ['bold' => true], 
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D1FAE5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                
                // Merge title
                $sheet->mergeCells('A1:F1');
                $sheet->mergeCells('A2:F2');
                
                // Column widths
                $sheet->getColumnDimension('A')->setWidth(30);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(15);
                $sheet->getColumnDimension('D')->setWidth(10);
                $sheet->getColumnDimension('E')->setWidth(15);
                $sheet->getColumnDimension('F')->setWidth(12);
                
                // Find ingredient header row
                $ingredientHeaderRow = 0;
                for ($i = 1; $i <= $lastRow; $i++) {
                    if ($sheet->getCell('A'.$i)->getValue() === 'STOK BAHAN BAKU') {
                        $ingredientHeaderRow = $i + 1;
                        break;
                    }
                }
                
                // Style ingredient header
                if ($ingredientHeaderRow > 0) {
                    $sheet->getStyle('A'.($ingredientHeaderRow-1).':F'.($ingredientHeaderRow-1))->applyFromArray([
                        'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'DC2626']],
                    ]);
                    $sheet->getStyle('A'.$ingredientHeaderRow.':F'.$ingredientHeaderRow)->applyFromArray([
                        'font' => ['bold' => true], 
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FEE2E2']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                    ]);
                }
                
                // Conditional formatting for status
                for ($i = 6; $i <= $lastRow; $i++) {
                    $statusCell = 'F'.$i;
                    if ($sheet->getCell($statusCell)->getValue() === 'RENDAH') {
                        $sheet->getStyle($statusCell)->getFont()->getColor()->setRGB('DC2626');
                        $sheet->getStyle($statusCell)->getFont()->setBold(true);
                    } else if ($sheet->getCell($statusCell)->getValue() === 'AMAN') {
                        $sheet->getStyle($statusCell)->getFont()->getColor()->setRGB('059669');
                    }
                }
            },
        ];
    }
}
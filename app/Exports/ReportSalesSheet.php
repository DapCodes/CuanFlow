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

class ReportSalesSheet implements FromArray, WithTitle, WithStyles, WithEvents
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
        return 'Rincian Penjualan';
    }

    public function array(): array
    {
        $outletId = auth()->user()->outlet_id;

        $sales = Sale::with('customer')
            ->where('outlet_id', $outletId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('status', 'completed')
            ->orderBy('created_at')
            ->get();

        $data = [
            ['RINCIAN TRANSAKSI PENJUALAN'],
            ['Periode: ' . $this->startDate->format('d M Y') . ' - ' . $this->endDate->format('d M Y')],
            ['Tanggal Cetak: ' . now()->format('d M Y H:i')],
            [''],
            ['DAFTAR TRANSAKSI'],
            [''],
            ['Tanggal', 'No. Invoice', 'Pelanggan', 'Metode Pembayaran', 'Total (Rp)'],
        ];

        foreach ($sales as $sale) {
            $data[] = [
                $sale->created_at->format('d/m/Y H:i'),
                $sale->invoice_number,
                $sale->customer ? $sale->customer->name : 'Umum',
                ucfirst($sale->payment_method),
                $sale->grand_total
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
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ],
            // Header tabel - Background kuning
            7 => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '000000']], 
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FDE68A']], // Kuning muda
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                
                // Merge cells
                $sheet->mergeCells('A1:E1');
                $sheet->mergeCells('A2:E2');
                $sheet->mergeCells('A3:E3');
                $sheet->mergeCells('A5:E5');
                
                // Row heights - lebih tinggi agar tidak sempit
                $sheet->getRowDimension(1)->setRowHeight(35);
                $sheet->getRowDimension(2)->setRowHeight(22);
                $sheet->getRowDimension(3)->setRowHeight(20);
                $sheet->getRowDimension(4)->setRowHeight(10); // Spacing
                $sheet->getRowDimension(5)->setRowHeight(25);
                $sheet->getRowDimension(6)->setRowHeight(10); // Spacing
                $sheet->getRowDimension(7)->setRowHeight(25);
                
                // Set height untuk semua data rows
                for ($i = 8; $i <= $lastRow; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(22);
                }
                
                // Column widths
                $sheet->getColumnDimension('A')->setWidth(22);
                $sheet->getColumnDimension('B')->setWidth(22);
                $sheet->getColumnDimension('C')->setWidth(28);
                $sheet->getColumnDimension('D')->setWidth(22);
                $sheet->getColumnDimension('E')->setWidth(22);
                
                // Number formatting
                $sheet->getStyle('E8:E'.$lastRow)->getNumberFormat()->setFormatCode('#,##0');
                
                // Border untuk header
                $sheet->getStyle('A1:E1')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']]
                    ]
                ]);
                
                $sheet->getStyle('A2:E3')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9CA3AF']]
                    ]
                ]);
                
                $sheet->getStyle('A5:E5')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']]
                    ]
                ]);
                
                // Border untuk tabel data - lebih tegas
                $sheet->getStyle('A7:E7')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']]
                    ]
                ]);
                
                $sheet->getStyle('A8:E'.$lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9CA3AF']]
                    ]
                ]);
                
                // Alternating row colors (abu-putih) untuk data
                for ($i = 8; $i <= $lastRow; $i++) {
                    if ($i % 2 == 0) {
                        // Even rows - putih
                        $sheet->getStyle('A'.$i.':E'.$i)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('FFFFFF');
                    } else {
                        // Odd rows - abu sangat muda
                        $sheet->getStyle('A'.$i.':E'.$i)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('F9FAFB');
                    }
                    
                    // Center alignment untuk kolom A (tanggal) dan B (invoice)
                    $sheet->getStyle('A'.$i.':B'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    
                    // Left alignment untuk kolom C & D (pelanggan & metode)
                    $sheet->getStyle('C'.$i.':D'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    
                    // Right alignment untuk kolom E (total)
                    $sheet->getStyle('E'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    
                    // Vertical center untuk semua
                    $sheet->getStyle('A'.$i.':E'.$i)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                }
            },
        ];
    }
}
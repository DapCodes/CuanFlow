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

class ReportHourlySheet implements FromArray, WithTitle, WithStyles, WithEvents
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
        return 'Analisis Waktu';
    }

    public function array(): array
    {
        $outletId = auth()->user()->outlet_id;

        $hourly = Sale::select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('SUM(grand_total) as revenue'),
                DB::raw('COUNT(*) as transactions')
            )
            ->where('outlet_id', $outletId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('status', 'completed')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        $data = [
            ['ANALISIS PENJUALAN PER JAM'],
            ['Periode: ' . $this->startDate->format('d M Y') . ' - ' . $this->endDate->format('d M Y')],
            [''],
            ['Jam', 'Jumlah Transaksi', 'Total Pendapatan (Rp)'],
        ];

        // Fill all 24 hours
        $hourlyData = [];
        foreach ($hourly as $h) {
            $hourlyData[$h->hour] = $h;
        }

        for ($i = 0; $i < 24; $i++) {
            $item = $hourlyData[$i] ?? null;
            $data[] = [
                sprintf('%02d:00 - %02d:59', $i, $i),
                $item ? $item->transactions : 0,
                $item ? $item->revenue : 0,
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'EA580C']], 
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
            2 => [
                'font' => ['italic' => true], 
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
            4 => [
                'font' => ['bold' => true], 
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FED7AA']],
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
                
                // Merge cells
                $sheet->mergeCells('A1:C1');
                $sheet->mergeCells('A2:C2');
                
                // Column widths
                $sheet->getColumnDimension('A')->setWidth(25);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(25);
                
                // Number formatting
                $sheet->getStyle('C5:C'.$lastRow)->getNumberFormat()->setFormatCode('#,##0');
                
                // Borders
                $sheet->getStyle('A4:C'.$lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]
                    ]
                ]);
                
                // Find peak hour
                $maxRevenue = 0;
                $peakRow = 0;
                for ($i = 5; $i <= $lastRow; $i++) {
                    $revenue = $sheet->getCell('C'.$i)->getValue();
                    if ($revenue > $maxRevenue) {
                        $maxRevenue = $revenue;
                        $peakRow = $i;
                    }
                }
                
                // Highlight peak hour
                if ($peakRow > 0) {
                    $sheet->getStyle('A'.$peakRow.':C'.$peakRow)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB('FEF3C7');
                    $sheet->getStyle('A'.$peakRow.':C'.$peakRow)->getFont()->setBold(true);
                }
            },
        ];
    }
}
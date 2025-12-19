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

class ReportCashierSheet implements FromArray, WithTitle, WithStyles, WithEvents
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
        return 'Performa Kasir';
    }

    public function array(): array
    {
        $outletId = auth()->user()->outlet_id;

        $performance = Sale::select(
                'cashier_id',
                DB::raw('SUM(grand_total) as total_revenue'),
                DB::raw('COUNT(*) as total_transactions')
            )
            ->with('cashier')
            ->where('outlet_id', $outletId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('status', 'completed')
            ->groupBy('cashier_id')
            ->orderByDesc('total_revenue')
            ->get();

        $data = [
            ['LAPORAN PERFORMA KASIR'],
            ['Periode: ' . $this->startDate->format('d M Y') . ' - ' . $this->endDate->format('d M Y')],
            [''],
            ['Nama Kasir', 'Total Transaksi', 'Total Pendapatan (Rp)', 'Rata-rata per Transaksi (Rp)'],
        ];

        foreach ($performance as $p) {
            $avg = $p->total_transactions > 0 ? $p->total_revenue / $p->total_transactions : 0;
            $data[] = [
                $p->cashier->name ?? 'Unknown',
                $p->total_transactions,
                $p->total_revenue,
                $avg
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '7C3AED']], 
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
            2 => [
                'font' => ['italic' => true], 
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
            4 => [
                'font' => ['bold' => true], 
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'DDD6FE']],
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
                $sheet->mergeCells('A1:D1');
                $sheet->mergeCells('A2:D2');
                
                // Column widths
                $sheet->getColumnDimension('A')->setWidth(30);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(30);
                
                // Number formatting
                $sheet->getStyle('C5:D'.$lastRow)->getNumberFormat()->setFormatCode('#,##0');
                
                // Borders
                $sheet->getStyle('A4:D'.$lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']]
                    ]
                ]);
                
                // Highlight top performer
                if ($lastRow >= 5) {
                    $sheet->getStyle('A5:D5')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEF3C7');
                }
            },
        ];
    }
}
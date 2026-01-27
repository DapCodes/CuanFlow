<?php

namespace App\Exports;

use App\Models\Sale;
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

class ReportCashierSheet implements FromArray, WithEvents, WithStyles, WithTitle
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
            ['Periode: '.$this->startDate->format('d M Y').' - '.$this->endDate->format('d M Y')],
            ['Tanggal Cetak: '.now()->format('d M Y H:i')],
            [''],
            ['RINGKASAN PERFORMA'],
            [''],
            ['Nama Kasir', 'Total Transaksi', 'Total Pendapatan (Rp)', 'Rata-rata per Transaksi (Rp)'],
        ];

        foreach ($performance as $p) {
            $avg = $p->total_transactions > 0 ? $p->total_revenue / $p->total_transactions : 0;
            $data[] = [
                $p->cashier->name ?? 'Unknown',
                $p->total_transactions,
                $p->total_revenue,
                $avg,
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
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ],
            // Header tabel - Background kuning
            7 => [
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

                // Merge cells
                $sheet->mergeCells('A1:D1');
                $sheet->mergeCells('A2:D2');
                $sheet->mergeCells('A3:D3');
                $sheet->mergeCells('A5:D5');

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
                $sheet->getColumnDimension('A')->setWidth(30);
                $sheet->getColumnDimension('B')->setWidth(22);
                $sheet->getColumnDimension('C')->setWidth(28);
                $sheet->getColumnDimension('D')->setWidth(32);

                // Number formatting
                $sheet->getStyle('B8:B'.$lastRow)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('C8:C'.$lastRow)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('D8:D'.$lastRow)->getNumberFormat()->setFormatCode('#,##0');

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

                // Border untuk tabel data - lebih tegas
                $sheet->getStyle('A7:D7')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']],
                    ],
                ]);

                $sheet->getStyle('A8:D'.$lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9CA3AF']],
                    ],
                ]);

                // Alternating row colors (abu-putih) untuk data
                for ($i = 8; $i <= $lastRow; $i++) {
                    if ($i % 2 == 0) {
                        // Even rows - putih
                        $sheet->getStyle('A'.$i.':D'.$i)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('FFFFFF');
                    } else {
                        // Odd rows - abu sangat muda
                        $sheet->getStyle('A'.$i.':D'.$i)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB('F9FAFB');
                    }

                    // Center alignment untuk kolom B (transaksi)
                    $sheet->getStyle('B'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Right alignment untuk kolom C & D (pendapatan)
                    $sheet->getStyle('C'.$i.':D'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    // Vertical center untuk semua
                    $sheet->getStyle('A'.$i.':D'.$i)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                }

                // Left alignment untuk kolom A (nama kasir)
                $sheet->getStyle('A8:A'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // Highlight top performer dengan kuning terang
                if ($lastRow >= 8) {
                    $sheet->getStyle('A8:D8')->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB('FEF08A'); // Kuning highlight
                    $sheet->getStyle('A8:D8')->getFont()->setBold(true);

                    // Border lebih tegas untuk top performer
                    $sheet->getStyle('A8:D8')->applyFromArray([
                        'borders' => [
                            'outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => 'EAB308']],
                        ],
                    ]);
                }
            },
        ];
    }
}

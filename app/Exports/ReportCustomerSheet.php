<?php

namespace App\Exports;

use App\Models\Sale;
use App\Models\CustomerDebt;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportCustomerSheet implements FromArray, WithTitle, WithStyles, WithEvents
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
        return 'Pelanggan';
    }

    public function array(): array
    {
        $outletId = auth()->user()->outlet_id;

        // Top Customers
        $topCustomers = Sale::select('customer_id', DB::raw('COUNT(*) as total_transactions'), DB::raw('SUM(grand_total) as total_spent'))
            ->with('customer')
            ->where('outlet_id', $outletId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('status', 'completed')
            ->whereNotNull('customer_id')
            ->groupBy('customer_id')
            ->orderByDesc('total_spent')
            ->take(20)
            ->get();

        // Customer Debts (Created in period)
        $customerDebts = CustomerDebt::with('customer')
            ->where('outlet_id', $outletId)
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->get();

        $totalPiutang = $customerDebts->where('status', '!=', 'paid')->sum('remaining_amount');

        $data = [
            ['LAPORAN PELANGGAN'],
            ['Periode: ' . $this->startDate->format('d M Y') . ' - ' . $this->endDate->format('d M Y')],
            ['Tanggal Cetak: ' . now()->format('d M Y H:i')],
            [''],
            ['RINGKASAN'],
            [''],
            ['Metrik', 'Nilai'],
            ['Total Piutang Pelanggan', $totalPiutang],
            ['Jumlah Pelanggan dengan Hutang', $customerDebts->count()],
            [''],
            [''],
            ['TOP 20 PELANGGAN TERLOYAL'],
            [''],
            ['No', 'Nama Pelanggan', 'Jumlah Transaksi', 'Total Belanja'],
        ];

        foreach ($topCustomers as $index => $cust) {
            $data[] = [
                $index + 1,
                $cust->customer->name ?? 'Pelanggan',
                $cust->total_transactions,
                $cust->total_spent,
            ];
        }

        $data[] = [''];
        $data[] = [''];
        $data[] = ['DAFTAR PIUTANG PELANGGAN'];
        $data[] = [''];
        $data[] = ['No', 'Nama Pelanggan', 'No. Invoice', 'Jumlah Piutang', 'Status'];

        foreach ($customerDebts as $index => $debt) {
            $data[] = [
                $index + 1,
                $debt->customer->name ?? 'Pelanggan',
                $debt->invoice_number ?? '-',
                $debt->remaining_amount,
                $debt->status === 'paid' ? 'Lunas' : ($debt->status === 'partial' ? 'Dibayar Sebagian' : 'Belum Lunas'),
            ];
        }

        $data[] = ['', '', 'TOTAL', $totalPiutang, ''];

        return $data;
    }

    public function styles(Worksheet $sheet): array
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
            12 => [
                'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '000000']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D1D5DB']], // Abu sedang
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER]
            ],
            14 => [
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
                
                // Merge title cells
                $sheet->mergeCells('A1:E1');
                $sheet->mergeCells('A2:E2');
                $sheet->mergeCells('A3:E3');
                $sheet->mergeCells('A5:E5');
                $sheet->mergeCells('A12:E12');
                
                // Row heights
                $sheet->getRowDimension(1)->setRowHeight(35);
                $sheet->getRowDimension(2)->setRowHeight(22);
                $sheet->getRowDimension(3)->setRowHeight(20);
                $sheet->getRowDimension(4)->setRowHeight(10); // Spacing
                $sheet->getRowDimension(5)->setRowHeight(25);
                $sheet->getRowDimension(6)->setRowHeight(10); // Spacing
                $sheet->getRowDimension(7)->setRowHeight(25);
                
                // Column widths
                $sheet->getColumnDimension('A')->setWidth(10);
                $sheet->getColumnDimension('B')->setWidth(32);
                $sheet->getColumnDimension('C')->setWidth(22);
                $sheet->getColumnDimension('D')->setWidth(25);
                $sheet->getColumnDimension('E')->setWidth(20);
                
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
                
                // Ringkasan summary
                $sheet->getStyle('A7:B7')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']]
                    ]
                ]);
                
                for ($i = 8; $i <= 9; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(22);
                }
                
                $sheet->getStyle('A8:B9')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9CA3AF']]
                    ]
                ]);
                
                // Alternating colors untuk ringkasan
                for ($i = 8; $i <= 9; $i++) {
                    $bgColor = ($i % 2 == 0) ? 'FFFFFF' : 'F9FAFB';
                    $sheet->getStyle('A'.$i.':B'.$i)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB($bgColor);
                    $sheet->getStyle('A'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle('B'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A'.$i.':B'.$i)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                }
                
                // Number formatting ringkasan
                $sheet->getStyle('B8')->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('B9')->getNumberFormat()->setFormatCode('#,##0');
                
                // Section Top Customers
                $sheet->getRowDimension(10)->setRowHeight(10);
                $sheet->getRowDimension(11)->setRowHeight(10);
                $sheet->getRowDimension(12)->setRowHeight(25);
                $sheet->getRowDimension(13)->setRowHeight(10);
                $sheet->getRowDimension(14)->setRowHeight(25);
                
                $sheet->getStyle('A12:E12')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']]
                    ]
                ]);
                
                $sheet->getStyle('A14:D14')->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']]
                    ]
                ]);
                
                // Find where debt section starts
                $debtSectionRow = 0;
                $debtHeaderRow = 0;
                for ($i = 15; $i <= $lastRow; $i++) {
                    if ($sheet->getCell('A'.$i)->getValue() === 'DAFTAR PIUTANG PELANGGAN') {
                        $debtSectionRow = $i;
                        $debtHeaderRow = $i + 2;
                        break;
                    }
                }
                
                $topCustomerEnd = $debtSectionRow > 0 ? $debtSectionRow - 3 : $lastRow;
                
                // Top Customers data
                for ($i = 15; $i <= $topCustomerEnd; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(22);
                }
                
                $sheet->getStyle('A15:D'.$topCustomerEnd)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9CA3AF']]
                    ]
                ]);
                
                // Alternating colors dan alignment untuk top customers
                for ($i = 15; $i <= $topCustomerEnd; $i++) {
                    $bgColor = ($i % 2 == 0) ? 'FFFFFF' : 'F9FAFB';
                    $sheet->getStyle('A'.$i.':D'.$i)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB($bgColor);
                    
                    $sheet->getStyle('A'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('B'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle('C'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('D'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('A'.$i.':D'.$i)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                }
                
                $sheet->getStyle('C15:C'.$topCustomerEnd)->getNumberFormat()->setFormatCode('#,##0');
                $sheet->getStyle('D15:D'.$topCustomerEnd)->getNumberFormat()->setFormatCode('#,##0');
                
                // Debt section
                if ($debtSectionRow > 0) {
                    $sheet->getRowDimension($debtSectionRow)->setRowHeight(25);
                    $sheet->getRowDimension($debtSectionRow + 1)->setRowHeight(10);
                    $sheet->getRowDimension($debtHeaderRow)->setRowHeight(25);
                    
                    $sheet->mergeCells('A'.$debtSectionRow.':E'.$debtSectionRow);
                    
                    $sheet->getStyle('A'.$debtSectionRow.':E'.$debtSectionRow)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '000000']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D1D5DB']],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']]
                        ]
                    ]);
                    
                    $sheet->getStyle('A'.$debtHeaderRow.':E'.$debtHeaderRow)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '000000']], 
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FDE68A']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '6B7280']]
                        ]
                    ]);
                    
                    // Debt data
                    for ($i = $debtHeaderRow + 1; $i <= $lastRow; $i++) {
                        $sheet->getRowDimension($i)->setRowHeight(22);
                    }
                    
                    $sheet->getStyle('A'.($debtHeaderRow+1).':E'.$lastRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9CA3AF']]
                        ]
                    ]);
                    
                    // Alternating colors untuk debt
                    for ($i = $debtHeaderRow + 1; $i <= $lastRow; $i++) {
                        $bgColor = ($i % 2 == 0) ? 'FFFFFF' : 'F9FAFB';
                        $sheet->getStyle('A'.$i.':E'.$i)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setRGB($bgColor);
                        
                        $sheet->getStyle('A'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('B'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        $sheet->getStyle('C'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('D'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                        $sheet->getStyle('E'.$i)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('A'.$i.':E'.$i)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    }
                    
                    $sheet->getStyle('D'.($debtHeaderRow+1).':D'.$lastRow)->getNumberFormat()->setFormatCode('#,##0');
                    
                    // Bold untuk total row
                    $sheet->getStyle('A'.$lastRow.':E'.$lastRow)->getFont()->setBold(true);
                    $sheet->getStyle('A'.$lastRow.':E'.$lastRow)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setRGB('FEF08A');
                }
            },
        ];
    }
}
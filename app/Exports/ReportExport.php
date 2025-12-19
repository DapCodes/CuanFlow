<?php

namespace App\Exports;

use App\Models\Expense;
use App\Models\Sale;
use App\Models\SaleItem;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Facades\DB;

class ReportExport implements WithMultipleSheets
{
    use Exportable;

    protected Carbon $startDate;
    protected Carbon $endDate;

    public function __construct(Carbon $startDate, Carbon $endDate)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function sheets(): array
    {
        return [
            new ReportSummarySheet($this->startDate, $this->endDate),
            new ReportSalesSheet($this->startDate, $this->endDate),
            new ReportExpensesSheet($this->startDate, $this->endDate),
        ];
    }
}

class ReportSummarySheet implements FromArray, WithTitle, WithStyles, WithEvents
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
        $sales = Sale::whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('status', 'completed')
            ->get();

        $expenses = Expense::whereBetween('expense_date', [$this->startDate, $this->endDate])->get();

        $totalRevenue = $sales->sum('grand_total');
        $totalTransactions = $sales->count();
        $totalExpenses = $expenses->sum('amount');
        
        // Calculate COGS
        $totalCogs = SaleItem::whereHas('sale', function($q) {
                $q->whereBetween('created_at', [$this->startDate, $this->endDate])
                  ->where('status', 'completed');
            })
            ->get()
            ->sum(function($item) {
                return $item->buy_price * $item->quantity;
            });

        $grossProfit = $totalRevenue - $totalCogs;
        $netProfit = $grossProfit - $totalExpenses;

        return [
            ['LAPORAN BISNIS KESELURUHAN'],
            ['Periode: ' . $this->startDate->format('d M Y') . ' - ' . $this->endDate->format('d M Y')],
            ['Tanggal Cetak: ' . now()->format('d M Y H:i')],
            [''],
            ['RINGKASAN KEUANGAN'],
            [''],
            ['Metrik', 'Nilai'],
            ['Total Pendapatan', $totalRevenue],
            ['Total Transaksi', $totalTransactions],
            ['Total Pengeluaran', $totalExpenses],
            ['Total HPP (Estimasi)', $totalCogs],
            ['Laba Kotor', $grossProfit],
            ['Laba Bersih', $netProfit],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['italic' => true]],
            5 => ['font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '2563EB']]],
            7 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E5E7EB']]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->getColumnDimension('A')->setWidth(30);
                $sheet->getColumnDimension('B')->setWidth(25);
                
                // Format Currency
                $sheet->getStyle('B8:B13')->getNumberFormat()->setFormatCode('#,##0');
                
                // Borders
                $sheet->getStyle('A7:B13')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);
            },
        ];
    }
}

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
        $sales = Sale::with('customer')
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->where('status', 'completed')
            ->orderBy('created_at')
            ->get();

        $data = [
            ['RINCIAN PENJUALAN'],
            ['Periode: ' . $this->startDate->format('d M Y') . ' - ' . $this->endDate->format('d M Y')],
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
            1 => ['font' => ['bold' => true, 'size' => 14]],
            4 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E5E7EB']]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                
                $sheet->getColumnDimension('A')->setWidth(20);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(15);
                $sheet->getColumnDimension('E')->setWidth(20);

                $sheet->getStyle('E5:E'.$lastRow)->getNumberFormat()->setFormatCode('#,##0');
                
                $sheet->getStyle('A4:E'.$lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);
            },
        ];
    }
}

class ReportExpensesSheet implements FromArray, WithTitle, WithStyles, WithEvents
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
        return 'Rincian Pengeluaran';
    }

    public function array(): array
    {
        $expenses = Expense::whereBetween('expense_date', [$this->startDate, $this->endDate])
            ->orderBy('expense_date')
            ->get();

        $data = [
            ['RINCIAN PENGELUARAN'],
            ['Periode: ' . $this->startDate->format('d M Y') . ' - ' . $this->endDate->format('d M Y')],
            [''],
            ['Tanggal', 'Kategori', 'Deskripsi', 'Jumlah (Rp)'],
        ];

        foreach ($expenses as $expense) {
            $data[] = [
                Carbon::parse($expense->expense_date)->format('d/m/Y'),
                $expense->category ?? '-',
                $expense->description,
                $expense->amount
            ];
        }

        return $data;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            4 => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'E5E7EB']]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                
                $sheet->getColumnDimension('A')->setWidth(15);
                $sheet->getColumnDimension('B')->setWidth(20);
                $sheet->getColumnDimension('C')->setWidth(35);
                $sheet->getColumnDimension('D')->setWidth(20);

                $sheet->getStyle('D5:D'.$lastRow)->getNumberFormat()->setFormatCode('#,##0');
                
                $sheet->getStyle('A4:D'.$lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);
            },
        ];
    }
}

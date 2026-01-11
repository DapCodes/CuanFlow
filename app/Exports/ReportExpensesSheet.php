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
        $outletId = auth()->user()->outlet_id;

        $query = Expense::with('category')
            ->where('outlet_id', $outletId)
            ->whereDate('expense_date', '>=', $this->startDate->toDateString())
            ->whereDate('expense_date', '<=', $this->endDate->toDateString())
            ->orderBy('expense_date');

        $allExpenses = $query->get();

        $expenses = $allExpenses->filter(fn($e) => $e->amount > 0);
        $income = $allExpenses->filter(fn($e) => $e->amount < 0);

        $data = [
            ['RINCIAN TRANSAKSI KAS (PENGELUARAN & PEMASUKAN LAIN)'],
            ['Periode: ' . $this->startDate->format('d M Y') . ' - ' . $this->endDate->format('d M Y')],
            ['Tanggal Cetak: ' . now()->format('d M Y H:i')],
            [''],
            ['DAFTAR PENGELUARAN OPERASIONAL'],
            [''],
            ['Tanggal', 'Kategori', 'Deskripsi', 'Jumlah (Rp)'],
        ];

        foreach ($expenses as $expense) {
            $data[] = [
                $expense->expense_date ? $expense->expense_date->format('d/m/Y') : '-',
                $expense->category->name ?? '-',
                $expense->description,
                (float)$expense->amount
            ];
        }

        $data[] = ['', '', 'TOTAL PENGELUARAN', (float)$expenses->sum('amount')];
        $data[] = [''];
        $data[] = [''];
        $data[] = ['DAFTAR PEMASUKAN LAIN'];
        $data[] = [''];
        $data[] = ['Tanggal', 'Kategori', 'Deskripsi', 'Jumlah (Rp)'];

        foreach ($income as $inc) {
            $data[] = [
                $inc->expense_date ? $inc->expense_date->format('d/m/Y') : '-',
                $inc->category->name ?? '-',
                $inc->description,
                abs((float)$inc->amount)
            ];
        }
        $data[] = ['', '', 'TOTAL PEMASUKAN', abs((float)$income->sum('amount'))];

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
                $sheet->mergeCells('A1:D1');
                $sheet->mergeCells('A2:D2');
                $sheet->mergeCells('A3:D3');
                $sheet->mergeCells('A5:D5');
                
                // Row heights
                $sheet->getRowDimension(1)->setRowHeight(35);
                $sheet->getRowDimension(2)->setRowHeight(22);
                $sheet->getRowDimension(3)->setRowHeight(20);
                $sheet->getRowDimension(4)->setRowHeight(10);
                $sheet->getRowDimension(5)->setRowHeight(25);
                $sheet->getRowDimension(6)->setRowHeight(10);
                $sheet->getRowDimension(7)->setRowHeight(25);
                
                // Find Income Header
                $incomeSectionRow = 0;
                $incomeHeaderRow = 0;
                for ($i = 1; $i <= $lastRow; $i++) {
                    if ($sheet->getCell('A'.$i)->getValue() === 'DAFTAR PEMASUKAN LAIN') {
                        $incomeSectionRow = $i;
                        $incomeHeaderRow = $i + 2;
                        break;
                    }
                }

                $expenseEndRow = $incomeSectionRow > 0 ? $incomeSectionRow - 2 : $lastRow;

                // Style Expense Section
                for ($i = 8; $i <= $expenseEndRow; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(22);
                    $bgColor = ($i % 2 == 0) ? 'FFFFFF' : 'F9FAFB';
                    $sheet->getStyle('A'.$i.':D'.$i)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($bgColor);
                }
                
                // Bold Total Row for Expenses
                $sheet->getStyle('A'.$expenseEndRow.':D'.$expenseEndRow)->getFont()->setBold(true);
                $sheet->getStyle('A'.$expenseEndRow.':D'.$expenseEndRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEE2E2');

                // Column widths
                $sheet->getColumnDimension('A')->setWidth(18);
                $sheet->getColumnDimension('B')->setWidth(22);
                $sheet->getColumnDimension('C')->setWidth(45);
                $sheet->getColumnDimension('D')->setWidth(25);
                
                // Number formatting
                $sheet->getStyle('D8:D'.$lastRow)->getNumberFormat()->setFormatCode('#,##0');
                
                // Borders
                $sheet->getStyle('A1:D1')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_MEDIUM);
                $sheet->getStyle('A7:D'.$expenseEndRow)->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9CA3AF']]]
                ]);
                
                // Style Income Section
                if ($incomeSectionRow > 0) {
                    $sheet->mergeCells('A'.$incomeSectionRow.':D'.$incomeSectionRow);
                    $sheet->getStyle('A'.$incomeSectionRow.':D'.$incomeSectionRow)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 12],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D1D5DB']],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM]]
                    ]);

                    $sheet->getStyle('A'.$incomeHeaderRow.':D'.$incomeHeaderRow)->applyFromArray([
                        'font' => ['bold' => true],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'D1FAE5']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
                    ]);

                    for ($i = $incomeHeaderRow + 1; $i <= $lastRow; $i++) {
                        $sheet->getRowDimension($i)->setRowHeight(22);
                        $bgColor = ($i % 2 == 0) ? 'FFFFFF' : 'F9FAFB';
                        $sheet->getStyle('A'.$i.':D'.$i)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($bgColor);
                    }

                    // Bold Total Row for Income
                    $sheet->getStyle('A'.$lastRow.':D'.$lastRow)->getFont()->setBold(true);
                    $sheet->getStyle('A'.$lastRow.':D'.$lastRow)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D1FAE5');

                    $sheet->getStyle('A'.$incomeHeaderRow.':D'.$lastRow)->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '9CA3AF']]]
                    ]);
                }

                // Global alignments
                $sheet->getStyle('A8:A'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D8:D'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            },
        ];
    }
}
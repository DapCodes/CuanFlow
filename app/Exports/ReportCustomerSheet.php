<?php

namespace App\Exports;

use App\Models\Sale;
use App\Models\CustomerDebt;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ReportCustomerSheet implements FromArray, WithTitle, WithStyles, WithColumnWidths, WithEvents
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

        // Customer Debts
        $customerDebts = CustomerDebt::with('customer')
            ->where('outlet_id', $outletId)
            ->where('status', 'unpaid')
            ->get();

        $totalPiutang = $customerDebts->sum('amount');

        $data = [
            ['LAPORAN PELANGGAN'],
            ['Periode: ' . $this->startDate->format('d M Y') . ' - ' . $this->endDate->format('d M Y')],
            ['Tanggal Cetak: ' . now()->format('d M Y H:i')],
            [''],
            ['RINGKASAN'],
            ['Total Piutang Pelanggan', 'Rp ' . number_format($totalPiutang, 0, ',', '.')],
            ['Jumlah Pelanggan dengan Hutang', $customerDebts->count() . ' pelanggan'],
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
                $debt->amount,
                'Belum Lunas',
            ];
        }

        $data[] = ['', '', 'TOTAL', $totalPiutang, ''];

        return $data;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 16]],
            5 => ['font' => ['bold' => true, 'size' => 12]],
            10 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 30,
            'C' => 25,
            'D' => 20,
            'E' => 15,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Format currency columns
                $lastRow = count($this->array());
                $sheet->getStyle('D:D')->getNumberFormat()->setFormatCode('#,##0');
            },
        ];
    }
}

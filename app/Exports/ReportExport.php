<?php

namespace App\Exports;

use App\Exports\ReportSummarySheet;
use App\Exports\ReportSalesSheet;
use App\Exports\ReportExpensesSheet;
use App\Exports\ReportStockSheet;
use App\Exports\ReportCashierSheet;
use App\Exports\ReportHourlySheet;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportExport implements WithMultipleSheets
{
    use Exportable;

    protected Carbon $startDate;
    protected Carbon $endDate;
    protected array $selectedSheets;

    public function __construct(Carbon $startDate, Carbon $endDate, array $selectedSheets = [])
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->selectedSheets = $selectedSheets;
    }

    public function sheets(): array
    {
        $sheets = [];

        if (in_array('summary', $this->selectedSheets)) {
            $sheets[] = new ReportSummarySheet($this->startDate, $this->endDate);
        }
        if (in_array('sales', $this->selectedSheets)) {
            $sheets[] = new ReportSalesSheet($this->startDate, $this->endDate);
        }
        if (in_array('expenses', $this->selectedSheets)) {
            $sheets[] = new ReportExpensesSheet($this->startDate, $this->endDate);
        }
        if (in_array('stock', $this->selectedSheets)) {
            $sheets[] = new ReportStockSheet();
        }
        if (in_array('cashier', $this->selectedSheets)) {
            $sheets[] = new ReportCashierSheet($this->startDate, $this->endDate);
        }
        if (in_array('hourly', $this->selectedSheets)) {
            $sheets[] = new ReportHourlySheet($this->startDate, $this->endDate);
        }

        return $sheets;
    }
}
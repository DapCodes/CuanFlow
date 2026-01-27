<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportExport implements WithMultipleSheets
{
    protected Carbon $startDate;

    protected Carbon $endDate;

    protected array $sheets;

    public function __construct(Carbon $startDate, Carbon $endDate, array $sheets = [])
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->sheets = $sheets;
    }

    public function sheets(): array
    {
        $exportSheets = [];

        if (in_array('summary', $this->sheets)) {
            $exportSheets[] = new ReportSummarySheet($this->startDate, $this->endDate);
        }

        if (in_array('sales', $this->sheets)) {
            $exportSheets[] = new ReportSalesSheet($this->startDate, $this->endDate);
        }

        if (in_array('expenses', $this->sheets)) {
            $exportSheets[] = new ReportExpensesSheet($this->startDate, $this->endDate);
        }

        if (in_array('stock', $this->sheets)) {
            $exportSheets[] = new ReportStockSheet;
        }

        if (in_array('cashier', $this->sheets)) {
            $exportSheets[] = new ReportCashierSheet($this->startDate, $this->endDate);
        }

        if (in_array('hourly', $this->sheets)) {
            $exportSheets[] = new ReportHourlySheet($this->startDate, $this->endDate);
        }

        if (in_array('finance', $this->sheets)) {
            $exportSheets[] = new ReportFinanceSheet($this->startDate, $this->endDate);
        }

        if (in_array('customer', $this->sheets)) {
            $exportSheets[] = new ReportCustomerSheet($this->startDate, $this->endDate);
        }

        return $exportSheets;
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderPaymentsReportExport implements FromCollection, WithHeadings
{
    private $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    public function headings(): array
    {
        return [
            '項次',
            '日期',
            '訂單編號',
            '類型',
            '金流方式',
            '分期期數',
            '狀態',
            '金額',
            '發票號碼',
            '發票日期',
            '備註',
            '收款行',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->collection;
    }
}

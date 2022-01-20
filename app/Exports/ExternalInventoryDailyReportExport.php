<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExternalInventoryDailyReportExport implements FromCollection, WithHeadings
{
    private $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    public function headings(): array
    {
        return [
            '庫存日期',
            '倉庫',
            'Item編號',
            '商品名稱',
            '規格一',
            '規格二',
            'POS品號',
            '庫存類型',
            '供應商',
            '到期日',
            '是否追加',
            '安全庫存量',
            '庫存量',
            '售價(未稅)',
            '平均成本(未稅)',
            '毛利率(未稅)',
            '庫存成本(未稅)',
            '低於安全庫存量'
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

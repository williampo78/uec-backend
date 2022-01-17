<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderRefundExport implements FromCollection, WithHeadings
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
            '退貨申請時間',
            '退貨申請單號',
            '訂單編號',
            '會員帳號',
            '狀態',
            '物流方式',
            '退貨完成時間',
            '退款方式',
            '退款狀態',
            '訂購人',
            '取件聯絡人',
            '取件聯絡手機',
            '取件地址',
            'Item編號',
            '商品名稱',
            '規格一',
            '規格二',
            '申請數量',
            '檢驗合格數量',
            '檢驗不合格數',
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

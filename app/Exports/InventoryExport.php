<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Sheet;

class InventoryExport implements FromCollection, WithHeadings
{
    private $collection;

    public function __construct($collection)
    {
        Sheet::macro('styleCells', function (Sheet $sheet, string $cellRange, array $style) {
            $sheet->getDelegate()->getStyle($cellRange)->applyFromArray($style);
        });
        $this->collection = $collection;
    }

    public function headings(): array
    {
        return [
            '倉庫',
            'Item編號',
            '商品名稱',
            '規格一',
            '規格二',
            'POS品號',
            '庫存類型',
            '安全庫存量',
            '庫存量',
            '售價(含稅)',
            '平均成本(含稅)',
            '毛利率',
            '庫存成本(含稅)',
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

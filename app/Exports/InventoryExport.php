<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Sheet;

class InventoryExport implements FromCollection, WithHeadings, WithEvents
{
    private $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {

                $array = [
                    'H:H',
                    'I:I',
                    'J:J',
                    'K:K',
                    'L:L',
                    'M:M'
                ];

                foreach ($array as $item){
                    $event->sheet->styleCells(
                        $item,
                        [
                            'alignment' => [
                                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                            ],
                        ]
                    );
                }

            },
        ];
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

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryExport implements FromCollection, WithHeadings, WithColumnWidths, WithStyles, WithStrictNullComparison
{
    private $collection;
    private $total_rows = 0;

    public function __construct($collection)
    {
        $this->collection = $collection;
        $this->total_rows += $this->collection->count();
    }

    public function headings(): array
    {
        $this->total_rows++;

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
            '上下架狀態',
            '售價(含稅)',
            '平均成本(含稅)',
            '毛利率',
            '庫存成本(含稅)',
            '低於安全庫存量',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 15,
            'C' => 30,
            'D' => 10,
            'E' => 10,
            'F' => 15,
            'G' => 10,
            'H' => 15,
            'I' => 10,
            'J' => 15,
            'K' => 15,
            'L' => 10,
            'M' => 15,
            'N' => 15,
            'O' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // 對齊方式
        $alignment_datas = [
            'left' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'N','O'],
            'center' => [],
            'right' => ['H', 'I', 'J', 'K', 'L', 'M'],
        ];

        foreach ($alignment_datas as $method => $columns) {
            switch ($method) {
                case 'left':
                    foreach ($columns as $column) {
                        $sheet->getStyle("{$column}2:{$column}{$this->total_rows}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    }
                    break;

                case 'center':
                    foreach ($columns as $column) {
                        $sheet->getStyle("{$column}2:{$column}{$this->total_rows}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    }
                    break;

                case 'right':
                    foreach ($columns as $column) {
                        $sheet->getStyle("{$column}2:{$column}{$this->total_rows}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    }
                    break;
            }
        }

        // 數字格式
        $number_format_datas = [
            [
                'format' => '#,##0.00',
                'columns' => ['K'],
            ],
            [
                'format' => '#,##0',
                'columns' => ['J', 'M'],
            ],
        ];

        foreach ($number_format_datas as $data) {
            foreach ($data['columns'] as $column) {
                $sheet->getStyle("{$column}2:{$column}{$this->total_rows}")
                    ->getNumberFormat()
                    ->setFormatCode($data['format']);
            }
        }

        return [
            'A1:O1' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
                'font' => [
                    'bold' => true,
                ],
            ],
            "A1:O{$this->total_rows}" => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
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

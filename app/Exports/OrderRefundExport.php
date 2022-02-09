<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrderRefundExport implements FromCollection, WithHeadings, WithColumnWidths, WithStyles, WithStrictNullComparison
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

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 20,
            'C' => 20,
            'D' => 20,
            'E' => 15,
            'F' => 10,
            'G' => 10,
            'H' => 20,
            'I' => 20,
            'J' => 10,
            'K' => 15,
            'L' => 15,
            'M' => 15,
            'N' => 30,
            'O' => 15,
            'P' => 30,
            'Q' => 15,
            'R' => 15,
            'S' => 10,
            'T' => 15,
            'U' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // 對齊方式
        $alignment_datas = [
            'left' => ['N', 'P', 'Q', 'R'],
            'center' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'O', 'S', 'T', 'U'],
            'right' => [],
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

        return [
            'A1:U1' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
                'font' => [
                    'bold' => true,
                ],
            ],
            "A1:U{$this->total_rows}" => [
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

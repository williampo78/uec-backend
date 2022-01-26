<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrderPaymentsReportExport implements FromCollection, WithHeadings, WithColumnWidths, WithStyles, WithStrictNullComparison
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

    public function columnWidths(): array
    {
        return [
            'A' => 10,
            'B' => 15,
            'C' => 20,
            'D' => 10,
            'E' => 20,
            'F' => 15,
            'G' => 15,
            'H' => 15,
            'I' => 15,
            'J' => 15,
            'K' => 15,
            'L' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // 對齊方式
        $alignment_datas = [
            'left' => ['C', 'D', 'E', 'G', 'K', 'L'],
            'center' => ['A', 'B', 'I', 'J'],
            'right' => ['F', 'H'],
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
                'format' => '#,##0',
                'columns' => ['H'],
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
            'A1:L1' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
                'font' => [
                    'bold' => true,
                ],
            ],
            "A1:L{$this->total_rows}" => [
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

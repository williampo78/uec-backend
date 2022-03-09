<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OrdersExport implements FromArray, WithHeadings, WithColumnWidths, WithStyles, WithStrictNullComparison
{
    private $orders;
    private $total_rows = 0;
    private $merge_cell_rows = [];

    /**
     * 欄位
     */
    private const COLUMNS = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO'];

    /**
     * 欄位寬度
     */
    private const WIDTHS = [10, 20, 20, 10, 20, 10, 10, 20, 10, 20, 20, 20, 20, 20, 20, 10, 20, 20, 10, 20, 20, 20, 30, 10, 10, 10, 15, 10, 10, 10, 10, 10, 10, 15, 10, 15, 10, 15, 10, 15, 15];

    /**
     * 水平對齊方式
     * left: l
     * center: c
     * right: r
     */
    private const ALIGNMENTS = ['c', 'c', 'c', 'l', 'c', 'l', 'c', 'c', 'r', 'c', 'c', 'c', 'c', 'c', 'l', 'r', 'c', 'c', 'r', 'c', 'l', 'l', 'l', 'l', 'l', 'r', 'r', 'r', 'r', 'r', 'r', 'r', 'r', 'r', 'r', 'r', 'r', 'r', 'r', 'r', 'l'];

    /**
     * 需合併儲存格的欄位
     */
    private const MERGE_CELL_COLUMNS = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S'];

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    /**
     * @return array
     */
    function array(): array
    {
        $body = [];

        $count = 1;
        foreach ($this->orders as $order) {
            $row = [];

            // 訂單時間
            $order->ordered_date = Carbon::parse($order->ordered_date)->format('Y-m-d H:i');

            // 訂單狀態
            $order->status_code = config('uec.order_status_code_options')[$order->status_code] ?? null;

            // 訂單成立當下是否免運
            $order->is_shipping_free = $order->is_shipping_free == 1 ? 'Y' : 'N';

            // 取消 / 作廢時間
            if (isset($order->cancelled_at)) {
                $order->cancelled_voided_at = Carbon::parse($order->cancelled_at)->format('Y-m-d H:i');
            } elseif (isset($order->voided_at)) {
                $order->cancelled_voided_at = Carbon::parse($order->voided_at)->format('Y-m-d H:i');
            } else {
                $order->cancelled_voided_at = null;
            }

            // 出貨時間
            if (isset($order->shipped_at)) {
                $order->shipped_at = Carbon::parse($order->shipped_at)->format('Y-m-d H:i');
            }

            // 到店時間
            if (isset($order->arrived_store_at)) {
                $order->arrived_store_at = Carbon::parse($order->arrived_store_at)->format('Y-m-d H:i');
            }

            // (宅配)配達時間
            if ($order->lgst_method == 'HOME' && isset($order->delivered_at)) {
                $order->home_dilivered_at = Carbon::parse($order->delivered_at)->format('Y-m-d H:i');
            } else {
                $order->home_dilivered_at = null;
            }

            // (超取)取件時間
            if ($order->lgst_method != 'HOME' && isset($order->delivered_at)) {
                $order->cvs_completed_at = Carbon::parse($order->delivered_at)->format('Y-m-d H:i');
            } else {
                $order->cvs_completed_at = null;
            }

            // 物流方式
            $order->lgst_method = config('uec.lgst_method_options')[$order->lgst_method] ?? null;

            // 付款方式
            $order->payment_method = config('uec.payment_method_options')[$order->payment_method] ?? null;

            // 發票開立時間
            if (isset($order->invoice_date)) {
                $order->invoice_date = Carbon::parse($order->invoice_date)->format('Y-m-d');
            }

            $order_columns = [
                $order->ordered_date,
                $order->order_no,
                $order->status_code,
                $order->member_account,
                $order->lgst_method,
                $order->shipping_fee,
                $order->is_shipping_free,
                $order->cart_campaign_discount,
                $order->cancelled_voided_at,
                $order->shipped_at,
                $order->arrived_store_at,
                $order->home_dilivered_at,
                $order->cvs_completed_at,
                $order->payment_method,
                '',
                $order->invoice_date,
                $order->invoice_no,
                $order->paid_amount,
            ];

            if (isset($order->order_details)) {
                $is_first = true;
                $merge_cell_first_row = $count + 1;
                foreach ($order->order_details as $order_detail) {
                    $ori_qty = $order_detail->qty + $order_detail->returned_qty;
                    $ori_campaign_discount = $order_detail->campaign_discount + $order_detail->returned_campaign_discount;
                    $ori_subtotal = $order_detail->subtotal + $order_detail->returned_subtotal;
                    $ori_point_discount = $order_detail->point_discount + $order_detail->returned_point_discount;
                    $ori_actual_subtotal = $ori_subtotal + $ori_point_discount;

                    $order_detail_columns = [
                        $order_detail->product_no,
                        $order_detail->item_no,
                        $order_detail->pos_item_no,
                        $order_detail->product_name,
                        $order_detail->spec_1_value,
                        $order_detail->spec_2_value,
                        $order_detail->selling_price,
                        $order_detail->unit_price,
                        $ori_qty,
                        $ori_campaign_discount,
                        $ori_subtotal,
                        $ori_point_discount,
                        $ori_actual_subtotal,
                        $order_detail->returned_qty,
                        $order_detail->returned_campaign_discount,
                        $order_detail->returned_subtotal,
                        $order_detail->returned_point_discount,
                        $order_detail->qty,
                        $order_detail->campaign_discount,
                        $order_detail->subtotal,
                        $order_detail->point_discount,
                        $order_detail->package_no,
                    ];

                    $row = [$count];
                    if ($is_first) {
                        $row = array_merge($row, $order_columns, $order_detail_columns);
                        $is_first = false;
                    } else {
                        $emtpy_columns = array_fill(0, count(self::MERGE_CELL_COLUMNS), '');
                        $row = array_merge($row, $emtpy_columns, $order_detail_columns);
                        $this->merge_cell_rows[$merge_cell_first_row] = $count + 1;
                    }

                    $body[] = $row;
                    $count++;
                }
            } else {
                $row = [$count++];
                $row = array_merge($row, $order_columns);
                $body[] = $row;
            }
        }

        $this->total_rows += count($body);

        return $body;
    }

    public function headings(): array
    {
        $this->total_rows++;

        return [
            '項次',
            '訂單時間',
            '訂單編號',
            '訂單狀態',
            '會員帳號',
            '物流方式',
            '運費',
            '訂單成立時免運',
            '滿額折抵',
            '取消/作廢時間',
            '出貨時間',
            '到店時間',
            '(宅配)配達時間',
            '(超取)取件時間',
            '付款方式',
            '分期期數',
            '發票日期',
            '發票號碼',
            '發票金額',
            '商品序號',
            'Item編號',
            'POS品號',
            '商品名稱',
            '規格一',
            '規格二',
            '售價',
            '商品活動價',
            '數量',
            '折抵金額',
            '小計',
            '點數折抵',
            '實收金額',
            '已退數量',
            '已退折抵金額',
            '已退小計',
            '已退點數折抵',
            '未退數量',
            '未退折抵金額',
            '未退小計',
            '未退點數折抵',
            '託運單號',
        ];
    }

    public function columnWidths(): array
    {
        $columnWidths = [];

        foreach (self::COLUMNS as $key => $column) {
            $columnWidths[$column] = self::WIDTHS[$key] ?? 10;
        }

        return $columnWidths;
    }

    public function styles(Worksheet $sheet)
    {
        $last_column = self::COLUMNS[array_key_last(self::COLUMNS)];

        foreach (self::COLUMNS as $key => $column) {
            if (isset(self::ALIGNMENTS[$key])) {
                switch (self::ALIGNMENTS[$key]) {
                    case 'l':
                        $sheet->getStyle("{$column}2:{$column}{$this->total_rows}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                        break;

                    case 'c':
                        $sheet->getStyle("{$column}2:{$column}{$this->total_rows}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        break;

                    case 'r':
                        $sheet->getStyle("{$column}2:{$column}{$this->total_rows}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                        break;
                }
            }

            // 合併儲存格
            if (in_array($column, self::MERGE_CELL_COLUMNS)) {
                if (!empty($this->merge_cell_rows)) {
                    foreach ($this->merge_cell_rows as $first_row => $end_row) {
                        $sheet->mergeCells("{$column}{$first_row}:{$column}{$end_row}");
                    }
                }
            }
        }

        return [
            "A1:{$last_column}1" => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
                'font' => [
                    'bold' => true,
                ],
            ],
            "A1:{$last_column}{$this->total_rows}" => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}

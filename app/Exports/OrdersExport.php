<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class OrdersExport implements FromArray, WithHeadings, WithEvents
{
    private $orders;
    private $total_rows = 0;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    /**
     * @return array
     */
    public function array(): array
    {
        $body = [];

        $count = 1;
        foreach ($this->orders as $order) {
            $row = [];

            // 訂單時間
            $order->ordered_date = Carbon::parse($order->ordered_date)->format('Y-m-d H:i');

            // 訂單狀態
            if (isset(config('uec.order_status_code_options')[$order->status_code])) {
                $order->status_code = config('uec.order_status_code_options')[$order->status_code];
            }

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
            if (isset(config('uec.order_lgst_method_options')[$order->lgst_method])) {
                $order->lgst_method = config('uec.order_lgst_method_options')[$order->lgst_method];
            }

            // 付款方式
            if (isset(config('uec.order_payment_method_options')[$order->payment_method])) {
                $order->payment_method = config('uec.order_payment_method_options')[$order->payment_method];
            }

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

                    $row = [$count++];
                    if ($is_first) {
                        $row = array_merge($row, $order_columns, $order_detail_columns);
                        $is_first = false;
                    } else {
                        $emtpy_columns = array_fill(0, 17, '');
                        $row = array_merge($row, $emtpy_columns, $order_detail_columns);
                    }

                    $body[] = $row;
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

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->styleCells(
                    'A1:AN1',
                    [
                        'alignment' => [
                            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ]
                );

                $alignment_datas = [
                    'left' => ['D', 'F', 'N', 'T', 'U', 'V', 'W', 'X', 'AN'],
                    'center' => ['A', 'B', 'C', 'E', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'P', 'Q', 'S'],
                    'right' => ['O', 'R', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM'],
                ];

                foreach ($alignment_datas as $method => $columns) {
                    switch ($method) {
                        case 'left':
                            foreach ($columns as $column) {
                                $event->sheet->getDelegate()->getStyle($column . '2:' . $column . $this->total_rows)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                            }
                            break;
                        case 'center':
                            foreach ($columns as $column) {
                                $event->sheet->getDelegate()->getStyle($column . '2:' . $column . $this->total_rows)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                            }
                            break;
                        case 'right':
                            foreach ($columns as $column) {
                                $event->sheet->getDelegate()->getStyle($column . '2:' . $column . $this->total_rows)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                            }
                            break;
                    }
                }

                $width_datas = [
                    'A' => 10,
                    'B' => 20,
                    'C' => 20,
                    'D' => 10,
                    'E' => 20,
                    'F' => 10,
                    'G' => 10,
                    'H' => 20,
                    'I' => 20,
                    'J' => 20,
                    'K' => 20,
                    'L' => 20,
                    'M' => 20,
                    'N' => 20,
                    'O' => 10,
                    'P' => 20,
                    'Q' => 20,
                    'R' => 10,
                    'S' => 20,
                    'T' => 20,
                    'U' => 20,
                    'V' => 30,
                    'W' => 10,
                    'X' => 10,
                    'Y' => 10,
                    'Z' => 15,
                    'AA' => 10,
                    'AB' => 10,
                    'AC' => 10,
                    'AD' => 10,
                    'AE' => 10,
                    'AF' => 10,
                    'AG' => 15,
                    'AH' => 10,
                    'AI' => 15,
                    'AJ' => 10,
                    'AK' => 15,
                    'AL' => 10,
                    'AM' => 15,
                    'AN' => 15,
                ];

                foreach ($width_datas as $cloumn => $width) {
                    $event->sheet->getDelegate()->getColumnDimension($cloumn)->setWidth($width);
                }
            },
        ];
    }
}

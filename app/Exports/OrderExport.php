<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class OrderExport implements FromCollection, WithHeadings, WithColumnWidths, WithStyles, WithStrictNullComparison
{
    private $orders;
    private $totalRows = 0;
    private $mergeCellRows = [];

    /**
     * 欄位
     */
    private const COLUMNS = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR'];

    /**
     * 欄位寬度
     */
    private const WIDTHS = [10, 20, 20, 10, 20, 10, 10, 20, 10, 20, 20, 20, 20, 20, 20, 10, 20, 20, 10, 20, 20, 20, 30, 10, 10, 10, 15, 10, 10, 10, 10, 10, 10, 10, 10, 15, 10, 15, 10, 15, 10, 10, 15, 15];

    /**
     * 水平對齊方式
     * left: l
     * center: c
     * right: r
     */
    private const ALIGNMENTS = ['c', 'c', 'c', 'l', 'c', 'l', 'c', 'c', 'r', 'c', 'c', 'c', 'c', 'c', 'l', 'r', 'c', 'c', 'r', 'c', 'l', 'l', 'l', 'l', 'l', 'r', 'r', 'r' ,'r', 'r', 'r', 'r', 'r', 'r', 'r', 'r', 'r', 'r', 'r', 'r', 'r', 'r', 'r', 'l'];

    /**
     * 需合併儲存格的欄位
     */
    private const MERGE_CELL_COLUMNS = ['B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S'];

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    public function collection(): Collection
    {
        $body = collect();

        $count = 1;
        foreach ($this->orders as $order) {
            $row = [
                'count' => $count,
                'ordered_date' => Carbon::parse($order->ordered_date)->format('Y-m-d H:i'),
                'order_no' => $order->order_no,
                'status_code' => config('uec.order_status_code_options')[$order->status_code] ?? null,
                'member_account' => $order->member_account,
                'lgst_method' => config('uec.lgst_method_options')[$order->lgst_method] ?? null,
                'shipping_fee' => $order->shipping_fee,
                'is_shipping_free' => $order->is_shipping_free == 1 ? 'Y' : 'N',
                'cart_campaign_discount' => $order->cart_campaign_discount,
                'cancelled_voided_at' => null,
                'shipped_at' => null,
                'arrived_store_at' => null,
                'home_dilivered_at' => null,
                'cvs_completed_at' => null,
                'payment_method' => config('uec.payment_method_options')[$order->payment_method] ?? null,
                'number_of_installments' => null,
                'invoice_date' => null,
                'invoice_no' => $order->invoice_no,
                'paid_amount' => $order->paid_amount,
                'product_no' => null,
                'item_no' => null,
                'pos_item_no' => null,
                'product_name' => null,
                'spec_1_value' => null,
                'spec_2_value' => null,
                'selling_price' => null,
                'unit_price' => null,
                'original_qty' => null,
                'original_campaign_discount' => null,
                'original_subtotal' => null,
                'original_point_discount' => null,
                'original_actual_subtotal' => null,
                'original_cart_p_discount' => null,
                'returned_qty' => null,
                'returned_campaign_discount' => null,
                'returned_subtotal' => null,
                'returned_cart_p_discount' => null,
                'returned_point_discount' => null,
                'qty' => null,
                'campaign_discount' => null,
                'subtotal' => null,
                'cart_p_discount' => null,
                'point_discount' => null,
                'package_no' => null,
            ];

            // 取消 / 作廢時間
            if (isset($order->cancelled_at)) {
                $row['cancelled_voided_at'] = Carbon::parse($order->cancelled_at)->format('Y-m-d H:i');
            } elseif (isset($order->voided_at)) {
                $row['cancelled_voided_at'] = Carbon::parse($order->voided_at)->format('Y-m-d H:i');
            }

            // 出貨時間
            if (isset($order->shipped_at)) {
                $row['shipped_at'] = Carbon::parse($order->shipped_at)->format('Y-m-d H:i');
            }

            // 到店時間
            if (isset($order->arrived_store_at)) {
                $row['arrived_store_at'] = Carbon::parse($order->arrived_store_at)->format('Y-m-d H:i');
            }

            // (宅配)配達時間
            if ($order->lgst_method == 'HOME' && isset($order->delivered_at)) {
                $row['home_dilivered_at'] = Carbon::parse($order->delivered_at)->format('Y-m-d H:i');
            }

            // (超取)取件時間
            if ($order->lgst_method != 'HOME' && isset($order->delivered_at)) {
                $row['cvs_completed_at'] = Carbon::parse($order->delivered_at)->format('Y-m-d H:i');
            }

            // 發票開立時間
            if (isset($order->invoice_date)) {
                $row['invoice_date'] = Carbon::parse($order->invoice_date)->format('Y-m-d');
            }

            if ($order->orderDetails->isNotEmpty()) {
                $mergeCellFirstRow = $count + 1;

                $order->orderDetails->each(function ($orderDetail) use (&$row, &$count, &$mergeCellFirstRow, &$body) {
                    $row['count'] = $count;
                    $row['product_no'] = $orderDetail->product->product_no;
                    $row['item_no'] = $orderDetail->item_no;
                    $row['pos_item_no'] = $orderDetail->productItem->pos_item_no;
                    $row['product_name'] = $orderDetail->product->product_name;
                    $row['spec_1_value'] = $orderDetail->productItem->spec_1_value;
                    $row['spec_2_value'] = $orderDetail->productItem->spec_2_value;
                    $row['selling_price'] = $orderDetail->selling_price;
                    $row['unit_price'] = $orderDetail->unit_price;
                    $row['original_qty'] = $orderDetail->qty - $orderDetail->returned_qty;
                    $row['original_campaign_discount'] = $orderDetail->campaign_discount - $orderDetail->returned_campaign_discount;
                    $row['original_subtotal'] = $orderDetail->subtotal - $orderDetail->returned_subtotal;
                    $row['original_point_discount'] = $orderDetail->point_discount - $orderDetail->returned_point_discount;
                    $row['original_actual_subtotal'] = $row['original_subtotal'] + $row['original_point_discount'];
                    $row['original_cart_p_discount'] = $orderDetail->cart_p_discount - $orderDetail->returned_cart_p_discount;
                    $row['returned_qty'] = $orderDetail->returned_qty;
                    $row['returned_campaign_discount'] = $orderDetail->returned_campaign_discount;
                    $row['returned_subtotal'] = $orderDetail->returned_subtotal;
                    $row['returned_cart_p_discount'] = $orderDetail->returned_cart_p_discount;
                    $row['returned_point_discount'] = $orderDetail->returned_point_discount;
                    $row['qty'] = $orderDetail->qty;
                    $row['campaign_discount'] = $orderDetail->campaign_discount;
                    $row['subtotal'] = $orderDetail->subtotal;
                    $row['cart_p_discount'] = $orderDetail->cart_p_discount;
                    $row['point_discount'] = $orderDetail->point_discount;

                    // 託運單號
                    if (isset($orderDetail->shipmentDetail)) {
                        $row['package_no'] = $orderDetail->shipmentDetail->shipment->package_no;//ar
                    }

                    $mergeCellLastRow = $count + 1;
                    if ($mergeCellFirstRow != $mergeCellLastRow) {
                        $this->mergeCellRows[$mergeCellFirstRow] = $mergeCellLastRow;
                    }

                    $body->push($row);
                    $count++;
                });
            } else {
                $body->push($row);
                $count++;
            }
        }

        $this->totalRows += $body->count();

        return $body;
    }

    public function headings(): array
    {
        $this->totalRows++;

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
            '單品折抵',
            '小計',
            '點數折抵',
            '實收金額',
            '購物車滿額折抵',
            '已退數量',
            '已退單品折抵',
            '已退小計',
            '已退購物車滿額折抵',
            '已退點數折抵',
            '未退數量',
            '未退單品折抵',
            '未退小計',
            '未退購物車滿額折抵',
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
                        $sheet->getStyle("{$column}2:{$column}{$this->totalRows}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                        break;

                    case 'c':
                        $sheet->getStyle("{$column}2:{$column}{$this->totalRows}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        break;

                    case 'r':
                        $sheet->getStyle("{$column}2:{$column}{$this->totalRows}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                        break;
                }
            }

            // 合併儲存格
            if (in_array($column, self::MERGE_CELL_COLUMNS)) {
                if (!empty($this->mergeCellRows)) {
                    foreach ($this->mergeCellRows as $first_row => $end_row) {
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
            "A1:{$last_column}{$this->totalRows}" => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}

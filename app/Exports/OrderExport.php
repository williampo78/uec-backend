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
                'count' => $count, //a
                'ordered_date' => Carbon::parse($order->ordered_date)->format('Y-m-d H:i'),//b
                'order_no' => $order->order_no,//c
                'status_code' => config('uec.order_status_code_options')[$order->status_code] ?? null,//d
                'member_account' => $order->member_account,//e
                'lgst_method' => config('uec.lgst_method_options')[$order->lgst_method] ?? null,//f
                'shipping_fee' => $order->shipping_fee,//g
                'is_shipping_free' => $order->is_shipping_free == 1 ? 'Y' : 'N',//h
                'cart_campaign_discount' => $order->cart_campaign_discount,//i
                'cancelled_voided_at' => null,//j
                'shipped_at' => null,//k
                'arrived_store_at' => null,//l
                'home_dilivered_at' => null,//m
                'cvs_completed_at' => null,//n
                'payment_method' => config('uec.payment_method_options')[$order->payment_method] ?? null,//o
                'number_of_installments' => null,//p
                'invoice_date' => null,//q
                'invoice_no' => $order->invoice_no,//r
                'paid_amount' => $order->paid_amount,//s
                'product_no' => null,//t
                'item_no' => null,//u
                'pos_item_no' => null,//v
                'product_name' => null,//w
                'spec_1_value' => null,//x
                'spec_2_value' => null,//y
                'selling_price' => null,//z
                'unit_price' => null,//aa
                'original_qty' => null,//ab
                'original_campaign_discount' => null,//ac
                'original_subtotal' => null,//ad
                'original_cart_p_discount' => null,//ae
                'original_point_discount' => null,//af
                'original_actual_subtotal' => null,//ag
                'returned_qty' => null,//ah
                'returned_campaign_discount' => null,//ai
                'returned_subtotal' => null,//aj
                'returned_cart_p_discount' => null,//ak
                'returned_point_discount' => null,//al
                'qty' => null,//am
                'campaign_discount' => null,//an
                'subtotal' => null,//ao
                'cart_p_discount' => null,//ap
                'point_discount' => null,//aq
                'package_no' => null,//ar
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
                    $row['product_no'] = $orderDetail->product->product_no;//t
                    $row['item_no'] = $orderDetail->item_no;//u
                    $row['pos_item_no'] = $orderDetail->productItem->pos_item_no;//v
                    $row['product_name'] = $orderDetail->product->product_name;//w
                    $row['spec_1_value'] = $orderDetail->productItem->spec_1_value;//x
                    $row['spec_2_value'] = $orderDetail->productItem->spec_2_value;//y
                    $row['selling_price'] = $orderDetail->selling_price;//z
                    $row['unit_price'] = $orderDetail->unit_price;//aa
                    $row['original_qty'] = $orderDetail->qty + $orderDetail->returned_qty;//ab
                    $row['original_campaign_discount'] = $orderDetail->campaign_discount + $orderDetail->returned_campaign_discount;//ac
                    $row['original_subtotal'] = $orderDetail->subtotal + $orderDetail->returned_subtotal;//ad小計
                    $row['original_cart_p_discount'] = $orderDetail->cart_p_discount + $orderDetail->returned_cart_p_discount;//ae
                    $row['original_point_discount'] = $orderDetail->point_discount + $orderDetail->returned_point_discount;//af
                    $row['original_actual_subtotal'] = $row['original_subtotal'] + $row['original_point_discount'];//ag
                    $row['returned_qty'] = $orderDetail->returned_qty;//ah
                    $row['returned_campaign_discount'] = $orderDetail->returned_campaign_discount;//ai
                    $row['returned_subtotal'] = $orderDetail->returned_subtotal;//aj
                    $row['returned_cart_p_discount'] = $orderDetail->returned_cart_p_discount;//ak
                    $row['returned_point_discount'] = $orderDetail->returned_point_discount;//al
                    $row['qty'] = $orderDetail->qty;//am
                    $row['campaign_discount'] = $orderDetail->campaign_discount;//an
                    $row['subtotal'] = $orderDetail->subtotal;//ao
                    $row['cart_p_discount'] = $orderDetail->cart_p_discount;//ap
                    $row['point_discount'] = $orderDetail->point_discount;//aq

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
            '項次',//a
            '訂單時間',//b
            '訂單編號',//c
            '訂單狀態',//d
            '會員帳號',//e
            '物流方式',//f
            '運費',//g
            '訂單成立時免運',//h
            '滿額折抵',//i
            '取消/作廢時間',//j
            '出貨時間',//k
            '到店時間',//l
            '(宅配)配達時間',//m
            '(超取)取件時間',//n
            '付款方式',//o
            '分期期數',//p
            '發票日期',//q
            '發票號碼',//r
            '發票金額',//s
            '商品序號',//t
            'Item編號',//u
            'POS品號',//v
            '商品名稱',//w
            '規格一',//x
            '規格二',//y
            '售價',//z
            '商品活動價',//aa
            '數量',//ab
            '單品折抵',//ac
            '小計',//ad
            '購物車滿額折抵',//ae
            '點數折抵',//af
            '實收金額',//ag
            '已退數量',//ah
            '已退單品折抵',//ai
            '已退小計',//aj
            '已退購物車滿額折抵',//ak
            '已退點數折抵',//al
            '未退數量',//am
            '未退單品折抵',//an
            '未退小計',//ao
            '未退購物車滿額折抵',//ap
            '未退點數折抵',//aq
            '託運單號',//ar
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

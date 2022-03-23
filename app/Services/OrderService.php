<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Shipment;
use App\Models\OrderDetail;
use App\Models\OrderPayment;
use App\Models\ProductPhoto;
use App\Models\InvoiceDetail;
use App\Models\ReturnRequest;
use App\Models\ShipmentDetail;
use App\Models\InvoiceAllowance;
use Illuminate\Support\Facades\DB;
use App\Models\OrderCampaignDiscount;
use App\Models\InvoiceAllowanceDetail;
use Illuminate\Database\Eloquent\Collection;

class OrderService
{
    public function getOrders($query_datas = [])
    {
        // 訂單
        $orders = Order::select(
            '*',
            DB::raw('get_order_status_desc(order_no) AS order_status_desc'),
        );

        if (isset($query_datas['id'])) {
            $orders = $orders->where('id', $query_datas['id']);
        }

        // CRM會員ID
        if (isset($query_datas['member_id'])) {
            $orders = $orders->where('member_id', $query_datas['member_id']);
        }

        // 退貨會導致一個order_no存在多筆orders，以此欄位標記是否為最新版本
        if (isset($query_datas['is_latest'])) {
            $orders = $orders->where('is_latest', $query_datas['is_latest']);
        }

        // 修訂版號
        if (isset($query_datas['revision_no'])) {
            $orders = $orders->where('revision_no', $query_datas['revision_no']);
        }

        // 訂單開始時間
        if (isset($query_datas['ordered_date_start'])) {
            $orders = $orders->whereDate('ordered_date', '>=', $query_datas['ordered_date_start']);
        }

        // 訂單結束時間
        if (isset($query_datas['ordered_date_end'])) {
            $orders = $orders->whereDate('ordered_date', '<=', $query_datas['ordered_date_end']);
        }

        // 訂單編號
        if (isset($query_datas['order_no'])) {
            $orders = $orders->where('order_no', $query_datas['order_no']);
        }

        // 會員帳號
        if (isset($query_datas['member_account'])) {
            $orders = $orders->where('member_account', $query_datas['member_account']);
        }

        // 訂單狀態
        if (isset($query_datas['order_status_code'])) {
            $orders = $orders->where('status_code', $query_datas['order_status_code']);
        }

        // 付款狀態
        if (isset($query_datas['pay_status'])) {
            $orders = $orders->where('pay_status', $query_datas['pay_status']);
        }

        $orders = $orders->orderBy('ordered_date', 'desc')
            ->get();

        $order_ids = $orders->pluck('id');
        $order_nos = $orders->pluck('order_no')->unique();

        // 訂單明細
        $order_details = OrderDetail::select(
            'order_details.*',

            'products.product_no',
            'products.product_name',

            'product_items.spec_1_value',
            'product_items.spec_2_value',
            'product_items.pos_item_no',
        )
            ->leftJoin('product_items', 'order_details.product_item_id', 'product_items.id')
            ->leftJoin('products', 'product_items.product_id', 'products.id');

        // 商品序號
        if (isset($query_datas['product_no'])) {
            $order_details = $order_details->where('products.product_no', $query_datas['product_no']);
        }

        // 商品名稱
        if (isset($query_datas['product_name'])) {
            $order_details = $order_details->where('products.product_name', 'LIKE', "%{$query_datas['product_name']}%");
        }

        $order_details = $order_details->whereIn('order_details.order_id', $order_ids)
            ->orderBy('order_details.order_id', 'asc')
            ->orderBy('order_details.seq', 'asc')
            ->get();

        $order_detail_product_ids = $order_details->pluck('product_id')->unique();

        // 商品圖片
        $product_photos = ProductPhoto::whereIn('product_id', $order_detail_product_ids)
            ->orderBy('product_id', 'asc')
            ->orderBy('sort', 'asc')
            ->get();

        // 出貨單
        $shipments = new Shipment;

        // 出貨單狀態
        if (isset($query_datas['shipment_status_code'])) {
            $shipments = $shipments->where('status_code', $query_datas['shipment_status_code']);
        }

        $shipments = $shipments->whereIn('order_no', $order_nos)
            ->get();

        $shipment_ids = $shipments->pluck('id');

        // 出貨單明細
        $shipment_details = ShipmentDetail::whereIn('shipment_id', $shipment_ids)
            ->get();

        // 將出貨單明細加入出貨單中
        foreach ($shipment_details as $shipment_detail) {
            if ($shipments->contains('id', $shipment_detail->shipment_id)) {
                $shipment = $shipments->firstWhere('id', $shipment_detail->shipment_id);

                // 檢查出貨單明細是否有定義
                if (!isset($shipment->shipment_details)) {
                    $shipment->shipment_details = collect();
                }

                $shipment->shipment_details->push($shipment_detail);
            }
        }

        // 訂單折扣
        $order_campaign_discounts = OrderCampaignDiscount::select(
            'order_campaign_discounts.*',

            'promotional_campaigns.campaign_name',
            'promotional_campaigns.level_code',

            'products.product_name',

            'product_items.spec_1_value',
            'product_items.spec_2_value',
        )
            ->leftJoin('promotional_campaigns', 'order_campaign_discounts.promotion_campaign_id', 'promotional_campaigns.id')
            ->leftJoin('product_items', 'order_campaign_discounts.product_item_id', 'product_items.id')
            ->leftJoin('products', 'product_items.product_id', 'products.id');

        // 活動名稱
        if (isset($query_datas['campaign_name'])) {
            $order_campaign_discounts = $order_campaign_discounts->where('promotional_campaigns.campaign_name', 'LIKE', "%{$query_datas['campaign_name']}%");
        }

        $order_campaign_discounts = $order_campaign_discounts->whereIn('order_campaign_discounts.order_id', $order_ids)
            ->orderBy('order_campaign_discounts.group_seq', 'asc')
            ->orderBy('order_campaign_discounts.order_detail_id', 'asc')
            ->get();

        // 發票開立
        $invoices = Invoice::select(
            'id AS invoice_id',
            'order_no',
            'invoice_date AS transaction_date',
            'invoice_no',
            'tax_type',
            'total_amount AS amount',
            'remark',
            'random_no',
            'cust_gui_number',
        )
            ->whereIn('order_no', $order_nos)
            ->get();

        $invoice_ids = $invoices->pluck('invoice_id');

        // 發票開立明細
        $invoice_details = InvoiceDetail::whereIn('invoice_id', $invoice_ids)
            ->orderBy('invoice_id', 'asc')
            ->orderBy('seq', 'asc')
            ->get();

        // 將發票開立明細加入發票開立中
        foreach ($invoice_details as $invoice_detail) {
            if ($invoices->contains('invoice_id', $invoice_detail->invoice_id)) {
                $invoice = $invoices->firstWhere('invoice_id', $invoice_detail->invoice_id);

                // 檢查發票開立明細是否有定義
                if (!isset($invoice->invoice_details)) {
                    $invoice->invoice_details = collect();
                }

                $invoice->invoice_details->push($invoice_detail);
            }
        }

        // 發票折讓
        $invoice_allowances = InvoiceAllowance::select(
            'invoice_allowance.id AS invoice_allowance_id',
            'invoice_allowance.order_no',
            'invoice_allowance.allowance_date AS transaction_date',
            'invoice_allowance.invoice_no',
            'invoices.tax_type',
            'invoice_allowance.allowance_amount AS amount',
            'invoices.remark',
            'invoices.random_no',
            'invoices.cust_gui_number',
        )
            ->join('invoices', 'invoice_allowance.invoice_id', 'invoices.id')
            ->whereIn('invoice_allowance.order_no', $order_nos)
            ->get();

        $invoice_allowance_ids = $invoice_allowances->pluck('invoice_allowance_id');

        // 發票折讓明細
        $invoice_allowance_details = InvoiceAllowanceDetail::whereIn('invoice_allowance_id', $invoice_allowance_ids)
            ->orderBy('invoice_allowance_id', 'asc')
            ->orderBy('seq', 'asc')
            ->get();

        // 將發票折讓明細加入發票折讓中
        foreach ($invoice_allowance_details as $invoice_allowance_detail) {
            if ($invoice_allowances->contains('invoice_allowance_id', $invoice_allowance_detail->invoice_allowance_id)) {
                $invoice_allowance = $invoice_allowances->firstWhere('invoice_allowance_id', $invoice_allowance_detail->invoice_allowance_id);

                // 檢查發票折讓明細是否有定義
                if (!isset($invoice_allowance->invoice_details)) {
                    $invoice_allowance->invoice_details = collect();
                }

                $invoice_allowance->invoice_details->push($invoice_allowance_detail);
            }
        }

        $all_invoices = $invoices->concat($invoice_allowances);
        $all_invoices = $all_invoices->sortBy('transaction_date');

        // 訂單金流單
        $order_payments = OrderPayment::whereIn('order_no', $order_nos)
            ->orderBy('created_at', 'asc')
            ->get();

        // 退貨申請單
        $return_requests = ReturnRequest::whereIn('order_id', $order_ids)
            ->orderBy('id', 'desc')
            ->get();

        foreach ($order_details as $order_detail) {
            // 將託運單號加入訂單明細中
            foreach ($shipments as $shipment) {
                if (isset($shipment->shipment_details)) {
                    if ($shipment->shipment_details->contains('order_detail_id', $order_detail->id)) {
                        $order_detail->package_no = $shipment->package_no;
                    }
                }
            }

            // 將商品圖片加入訂單明細中
            if ($product_photos->contains('product_id', $order_detail->product_id)) {
                $product_photo = $product_photos->firstWhere('product_id', $order_detail->product_id);

                // 檢查商品圖片是否有定義
                if (!isset($order_detail->product_photos)) {
                    $order_detail->product_photos = collect();
                }

                $order_detail->product_photos->push($product_photo);
            }

            // 將訂單明細加入訂單中
            if ($orders->contains('id', $order_detail->order_id)) {
                $order = $orders->firstWhere('id', $order_detail->order_id);

                // 檢查訂單明細是否有定義
                if (!isset($order->order_details)) {
                    $order->order_details = collect();
                }

                $order->order_details->push($order_detail);
            }
        }

        // 將出貨單加入訂單中
        foreach ($shipments as $shipment) {
            if ($orders->contains('order_no', $shipment->order_no)) {
                $order = $orders->firstWhere('order_no', $shipment->order_no);

                // 檢查出貨單是否有定義
                if (!isset($order->shipments)) {
                    $order->shipments = collect();
                }

                $order->shipments->push($shipment);
            }
        }

        // 將訂單折扣加入訂單中
        foreach ($order_campaign_discounts as $order_campaign_discount) {
            if ($orders->contains('id', $order_campaign_discount->order_id)) {
                $order = $orders->firstWhere('id', $order_campaign_discount->order_id);

                // 檢查訂單折扣是否有定義
                if (!isset($order->order_campaign_discounts)) {
                    $order->order_campaign_discounts = collect();
                }

                $order->order_campaign_discounts->push($order_campaign_discount);
            }
        }

        // 將發票資訊加入訂單中
        foreach ($all_invoices as $invoice) {
            if ($orders->contains('order_no', $invoice->order_no)) {
                $order = $orders->firstWhere('order_no', $invoice->order_no);

                // 檢查發票資訊是否有定義
                if (!isset($order->invoices)) {
                    $order->invoices = collect();
                }

                $order->invoices->push($invoice);
            }
        }

        // 將訂單金流單加入訂單中
        foreach ($order_payments as $order_payment) {
            if ($orders->contains('order_no', $order_payment->order_no)) {
                $order = $orders->firstWhere('order_no', $order_payment->order_no);

                // 檢查訂單金流單是否有定義
                if (!isset($order->order_payments)) {
                    $order->order_payments = collect();
                }

                $order->order_payments->push($order_payment);
            }
        }

        // 將退貨申請單加入訂單中
        foreach ($return_requests as $return_request) {
            if ($orders->contains('id', $return_request->order_id)) {
                $order = $orders->firstWhere('id', $return_request->order_id);

                // 檢查退貨申請單是否有定義
                if (!isset($order->return_requests)) {
                    $order->return_requests = collect();
                }

                $order->return_requests->push($return_request);
            }
        }

        $orders = $orders->filter(function ($order) use ($query_datas) {
            if ((isset($query_datas['product_no'])
                || isset($query_datas['product_name'])
            )
                && !isset($order->order_details)
            ) {
                return false;
            }

            if (isset($query_datas['shipment_status_code'])
                && !isset($order->shipments)
            ) {
                return false;
            }

            if (isset($query_datas['campaign_name'])
                && !isset($order->order_campaign_discounts)
            ) {
                return false;
            }

            return true;
        });

        return $orders;
    }

    /**
     * 取得訂單table列表
     *
     * @param array $payload
     * @return Collection
     */
    public function getTableList(array $payload = []): Collection
    {
        $orders = Order::with(['shipments'])->where('is_latest', 1);

        // 訂單開始時間
        if (isset($payload['ordered_date_start'])) {
            $orders = $orders->whereDate('ordered_date', '>=', $payload['ordered_date_start']);
        }

        // 訂單結束時間
        if (isset($payload['ordered_date_end'])) {
            $orders = $orders->whereDate('ordered_date', '<=', $payload['ordered_date_end']);
        }

        // 訂單編號
        if (isset($payload['order_no'])) {
            $orders = $orders->where('order_no', $payload['order_no']);
        }

        // 會員帳號
        if (isset($payload['member_account'])) {
            $orders = $orders->where('member_account', $payload['member_account']);
        }

        // 訂單狀態
        if (isset($payload['order_status_code'])) {
            $orders = $orders->where('status_code', $payload['order_status_code']);
        }

        // 付款狀態
        if (isset($payload['pay_status'])) {
            $orders = $orders->where('pay_status', $payload['pay_status']);
        }

        // 出貨單狀態
        if (isset($payload['shipment_status_code'])) {
            $orders = $orders->whereRelation('shipments', 'status_code', $payload['shipment_status_code']);
        }

        // 商品序號
        if (isset($payload['product_no'])) {
            $orders = $orders->whereRelation('orderDetails.product', 'product_no', $payload['product_no']);
        }

        // 商品名稱
        if (isset($payload['product_name'])) {
            $orders = $orders->whereRelation('orderDetails.product', 'product_name', 'LIKE', "%{$payload['product_name']}%");
        }

        // 活動名稱
        if (isset($payload['campaign_name'])) {
            $orders = $orders->whereRelation('orderCampaignDiscounts.promotionalCampaign', 'campaign_name', 'LIKE', "%{$payload['campaign_name']}%");
        }

        return $orders->select()
            ->addSelect(DB::raw('get_order_status_desc(order_no) AS order_status_desc'))
            ->get();
    }

    /**
     * 是否可以取消訂單
     *
     * @param string $status_code 訂單狀態
     * @param string $order_date 訂單成立時間
     * @param integer $cancel_limit_mins 訂單取消限制時間
     * @return boolean
     */
    public function canCancelOrder(string $status_code, string $order_date, int $cancel_limit_mins): bool
    {
        $now = Carbon::now();
        $cancel_limit_date = Carbon::parse($order_date)->addMinutes($cancel_limit_mins);

        if ($status_code != 'CREATED') {
            return false;
        }

        // 現在時間>訂單取消限制時間
        if ($now->greaterThan($cancel_limit_date)) {
            return false;
        }

        return true;
    }

    /**
     * 是否可以申請退貨
     *
     * @param string $status_code 訂單狀態
     * @param string|null $delivered_at 商品配達時間
     * @param string|null $cooling_off_due_date 鑑賞期截止時間
     * @param integer|null $return_request_id 退貨申請單id
     * @return boolean
     */
    public function canReturnOrder(string $status_code, ?string $delivered_at, ?string $cooling_off_due_date, ?int $return_request_id): bool
    {
        $now = Carbon::now();
        $cooling_off_due_date = Carbon::parse($cooling_off_due_date);

        if ($status_code != 'CLOSED') {
            return false;
        }

        if (isset($return_request_id)) {
            return false;
        }

        if (!isset($delivered_at) || !isset($cooling_off_due_date)) {
            return false;
        }

        // 現在時間>鑑賞期截止時間
        if ($now->greaterThan($cooling_off_due_date)) {
            return false;
        }

        return true;
    }
}

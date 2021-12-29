<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceAllowance;
use App\Models\InvoiceAllowanceDetail;
use App\Models\InvoiceDetail;
use App\Models\Order;
use App\Models\OrderCampaignDiscount;
use App\Models\OrderDetail;
use App\Models\OrderPayment;
use App\Models\Shipment;
use App\Models\ShipmentDetail;

class OrderService
{
    public function getOrders($query_datas = [])
    {
        // 訂單
        $orders = new Order;

        if (isset($query_datas['id'])) {
            $orders = $orders->where('id', $query_datas['id']);
        }

        // 退貨會導致一個order_no存在多筆orders，以此欄位標記是否為最新版本
        if (isset($query_datas['is_latest'])) {
            $orders = $orders->where('is_latest', $query_datas['is_latest']);
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

        // 訂單明細
        $order_details = OrderDetail::select(
            'order_details.*',

            'products.product_no',
            'products.product_name',
            'product_items.spec_1_value',
            'product_items.spec_2_value',
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

        $order_details = $order_details->orderBy('order_details.order_id', 'asc')
            ->orderBy('order_details.seq', 'asc')
            ->get();

        // 出貨單
        $shipments = new Shipment;

        // 出貨單狀態
        if (isset($query_datas['shipment_status_code'])) {
            $shipments = $shipments->where('status_code', $query_datas['shipment_status_code']);
        }

        $shipments = $shipments->get();

        // 出貨單明細
        $shipment_details = ShipmentDetail::get();

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
        )
            ->leftJoin('promotional_campaigns', 'order_campaign_discounts.promotion_campaign_id', 'promotional_campaigns.id');

        // 活動名稱
        if (isset($query_datas['campaign_name'])) {
            $order_campaign_discounts = $order_campaign_discounts->where('promotional_campaigns.campaign_name', 'LIKE', "%{$query_datas['campaign_name']}%");
        }

        $order_campaign_discounts = $order_campaign_discounts->get();

        $lookup_values_v_service = new LookupValuesVService;
        // 發票捐贈機構
        $donated_institutions = $lookup_values_v_service->getDonatedInstitutions();

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
        )
            ->get();

        // 發票開立明細
        $invoice_details = InvoiceDetail::orderBy('invoice_id', 'asc')
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
        )
            ->join('invoices', 'invoice_allowance.invoice_id', 'invoices.id')
            ->get();

        // 發票折讓明細
        $invoice_allowance_details = InvoiceAllowanceDetail::orderBy('invoice_allowance_id', 'asc')
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
        $order_payments = OrderPayment::orderBy('created_at', 'asc')
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

        // 將發票捐贈機構名稱加入訂單中
        foreach ($donated_institutions as $donated_institution) {
            if ($orders->contains('donated_institution', $donated_institution->code)) {
                $order = $orders->firstWhere('donated_institution', $donated_institution->code);
                $order->donated_institution_name = $donated_institution->description;
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

        // dump
        foreach ($orders as $order) {
            // dump($order->id);

            if (isset($order->order_details)) {
                // dump('order_details', $order->order_details->toArray());
            }

            if (isset($order->shipments)) {
                // dump('shipments', $order->shipments->toArray());

                foreach ($order->shipments as $shipment) {
                    if (isset($shipment->shipment_details)) {
                        // dump('shipment_details', $shipment->shipment_details->toArray());
                    }
                }
            }

            if (isset($order->order_campaign_discounts)) {
                // dump('order_campaign_discounts', $order->order_campaign_discounts->toArray());
            }
        }

        // dd($orders->toArray());
        // dd('end');

        return $orders;
    }
}

<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderCampaignDiscount;
use App\Models\OrderDetail;
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

<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\ShipmentDetail;
use App\Models\ShipmentProgressLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class ShipmentService
{
    public function getShipments($query_datas = [])
    {
        // 出貨單、訂單
        $shipments = Shipment::select(
            'shipments.id AS shipments_id',
            'shipments.shipment_no',
            'shipments.status_code',
            'shipments.payment_method',
            'shipments.lgst_method',
            'shipments.order_no',
            'shipments.ship_to_city',
            'shipments.ship_to_district',
            'shipments.ship_to_address',
            'shipments.ship_to_name',
            'shipments.ship_to_mobile',
            'shipments.package_no',
            'shipments.cancelled_at',
            'shipments.voided_at',
            'shipments.shipped_at',
            'shipments.arrived_store_at',
            'shipments.delivered_at',
            'shipments.overdue_confirmed_at',
            'shipments.created_at',
            'shipments.lgst_company_code',
            DB::raw('(case when orders.ship_from_whs = "SUP" then (select lk.description from lookup_values_v lk where lk.type_code = "SUP_LGST_COMPANY" and lk.code = shipments.sup_lgst_company)
                else shipments.lgst_company_code end) as lgst_company'),
            'shipments.edi_exported_at',

            'orders.id AS orders_id',
            'orders.member_account',
            'orders.buyer_name',
            'orders.ship_from_whs',

            'supplier.name AS supplier_name',
        )
            ->leftJoin('orders', 'shipments.order_id', '=', 'orders.id')
            ->leftJoin('supplier', 'shipments.supplier_id', '=', 'supplier.id');

        // 出貨單id
        if (isset($query_datas['shipment_id'])) {
            $shipments = $shipments->where('shipments.id', $query_datas['shipment_id']);
        }

        // 建單開始時間
        if (isset($query_datas['created_at_start'])) {
            $shipments = $shipments->whereDate('shipments.created_at', '>=', $query_datas['created_at_start']);
        }

        // 建單結束時間
        if (isset($query_datas['created_at_end'])) {
            $shipments = $shipments->whereDate('shipments.created_at', '<=', $query_datas['created_at_end']);
        }

        // 出貨單號
        if (isset($query_datas['shipment_no'])) {
            $shipments = $shipments->where('shipments.shipment_no', $query_datas['shipment_no']);
        }

        // 會員帳號
        if (isset($query_datas['member_account'])) {
            $shipments = $shipments->where('orders.member_account', $query_datas['member_account']);
        }

        // 訂單編號
        if (isset($query_datas['order_no'])) {
            $shipments = $shipments->where('shipments.order_no', $query_datas['order_no']);
        }

        // 出貨單狀態
        if (isset($query_datas['status_code'])) {
            $shipments = $shipments->where('shipments.status_code', $query_datas['status_code']);
        }

        // 付款方式
        if (isset($query_datas['payment_method'])) {
            $shipments = $shipments->where('shipments.payment_method', $query_datas['payment_method']);
        }

        $shipments = $shipments->orderBy('shipments.created_at', 'desc')
            ->get();

        // 出貨單明細
        $shipment_details = ShipmentDetail::select(
            'shipment_details.*',

            'products.product_no',
            'products.product_name',
            'products.supplier_product_no',

            'product_items.spec_1_value',
            'product_items.spec_2_value',
            'product_items.supplier_item_no',

            'supplier.name AS supplier_name',
        )
            ->leftJoin('product_items', 'shipment_details.product_item_id', 'product_items.id')
            ->leftJoin('products', 'product_items.product_id', 'products.id')
            ->leftJoin('shipments', 'shipment_details.shipment_id', '=', 'shipments.id')
            ->leftJoin('supplier', 'shipments.supplier_id', '=', 'supplier.id');

        // 商品序號
        if (isset($query_datas['product_no'])) {
            $shipment_details = $shipment_details->where('products.product_no', $query_datas['product_no']);
        }

        // 商品名稱
        if (isset($query_datas['product_name'])) {
            $shipment_details = $shipment_details->where('products.product_name', 'LIKE', "%{$query_datas['product_name']}%");
        }

        $shipment_details = $shipment_details->orderBy('shipment_details.shipment_id', 'asc')
            ->orderBy('shipment_details.seq', 'asc')
            ->get();

        // 將出貨單明細加入出貨單中
        foreach ($shipment_details as $shipment_detail) {
            if ($shipments->contains('shipments_id', $shipment_detail->shipment_id)) {
                $shipment = $shipments->firstWhere('shipments_id', $shipment_detail->shipment_id);

                // 檢查出貨單明細是否有定義
                if (!isset($shipment->shipment_details)) {
                    $shipment->shipment_details = collect();
                }

                $shipment->shipment_details->push($shipment_detail);
            }
        }

        $shipments = $shipments->filter(function ($shipment) use ($query_datas) {
            if ((isset($query_datas['product_no'])
                || isset($query_datas['product_name'])
            )
                && !isset($shipment->shipment_details)
            ) {
                return false;
            }

            return true;
        });

        return $shipments;
    }

    /**
     * 取得出貨配送歷程
     *
     * @param int $id
     * @return \Illuminate\Support\Collection
     */
    public function getProgressLogs(int $id): Collection
    {
        return ShipmentProgressLog::with([
            'supShipProgress',
            'loggedBy',
        ])
            ->where('shipment_id', $id)
            ->get();
    }
}

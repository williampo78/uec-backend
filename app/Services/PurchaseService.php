<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function __construct()
    {
    }
    public function getPurchase($in)
    {
        $purchase = Purchase::select(
            DB::raw('purchase.*'),
            DB::raw('order_supplier.number as order_supplier_number'),
            DB::raw('supplier.name as supplier_name'),

        )
            ->leftJoin('supplier', 'supplier.id', '=', 'purchase.supplier_id')
            ->leftJoin('order_supplier', 'order_supplier.id', '=', 'purchase.order_supplier_id');

        if (isset($in['supplier']) && $in['supplier']) { //供應商
            $purchase->where('purchase.supplier_id', $in['supplier']);
        }

        if (isset($in['company_number']) && $in['company_number']) { //供應商統編
            $purchase->where('supplier.company_number', $in['company_number']);
        }

        if (isset($in['order_supplier_number']) && $in['order_supplier_number']) { //採購單單號
            $purchase->where('order_supplier.number', $in['order_supplier_number']);
        }
        if (isset($in['trade_date_start']) && $in['trade_date_start'] && isset($in['trade_date_end']) && $in['trade_date_end']) { //進貨日期
            $purchase->whereBetween('purchase.trade_date', [$in['trade_date_start'], $in['trade_date_end']]);
        }
        if (isset($in['number']) && $in['number']) { //進貨單號
            $purchase->where('purchase.number', $in['number']);
        }
        if (isset($in['id']) && $in['id']) {
            $purchase->where('purchase.id', $in['id']);
            return $purchase->first();
        }
        return $purchase->get();
    }
    public function getPurchaseDetail($in)
    {
        $purchase = PurchaseDetail::select(
            DB::raw('purchase_detail.*'),
            DB::raw('product_items.product_id as product_id'),
            DB::raw('product_items.spec_1_value as spec_1_value'),
            DB::raw('product_items.spec_2_value as spec_2_value'),
            DB::raw('product_items.pos_item_no as pos_item_no'),
            DB::raw('product_items.ean as ean'),
            DB::raw('products.product_name as product_name'),
            DB::raw('products.uom as uom'),
            DB::raw('products.brand_id as brand_id'),
            DB::raw('product_items.item_no as product_items_no'),
            DB::raw('warehouse.name as warehouse_name'),

        )
            ->leftJoin('product_items', 'product_items.id', '=', 'purchase_detail.product_item_id')
            ->leftJoin('products', 'products.id', '=', 'product_items.product_id')
            ->leftJoin('warehouse', 'warehouse.id', '=', 'purchase_detail.warehouse_id')

        ;
        $purchase->where('purchase_id', $in['purchase_id']);

        return $purchase->get();
    }
}

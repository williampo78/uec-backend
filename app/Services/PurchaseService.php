<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function getPurchase($in)
    {
        $purchase = Purchase::with([
            'purchaseDetail',
            'purchaseDetail.warehouse',
            'purchaseDetail.productItem',
            'purchaseDetail.productItem.product',
            'purchaseDetail.productItem.product.brand',
            'supplier',
        ])
            ->whereHas('orderSupplier', function ($query) use ($in) {
                if (!empty($in['order_supplier_number'])) {
                    $query = $query->where('number', $in['order_supplier_number']);
                }
                return $query;
            })
            ->when(!empty($in['supplier']), function ($query) use ($in) {
                $query->where('supplier_id', $in['supplier']);
            })->when(!empty($in['company_number']), function ($query) use ($in) {
            $query->where('company_number', $in['company_number']);
        })->when(!empty($in['trade_date_start'] && !empty($in['trade_date_end'])), function ($query) use ($in) {
            $query->whereBetween('purchase.trade_date', [$in['trade_date_start'] . ' 00:00:00', $in['trade_date_end'] . ' 23:59:59']);
        })->when(!empty($in['number'] && !empty($in['number'])), function ($query) use ($in) {
            $query->where('number', $in['number']);
        })->when(!empty($in['id']), function ($query) use ($in) {
            $query->where('id', $in['id']);
        });

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

    public function updateInvoice($in)
    {
        $user_id = Auth::user()->id;
        return Purchase::where('id', $in['id'])->update([
            'updated_by' => $user_id,
            'invoice_number' => $in['invoice_number'],
            'invoice_date' => $in['invoice_date'],
        ]);
    }

    /**
     * 取得 買斷商品對帳單
     *
     */
    public function getBuyOutProducts($in)
    {
        $purchase = Purchase::select(
            DB::raw('purchase.*'),
            DB::raw('supplier.name as supplier_name'),
            DB::raw('order_supplier.tax as order_supplier_tax'),
            DB::raw('product_items.spec_1_value as spec_1_value'),
            DB::raw('product_items.spec_2_value as spec_2_value'),
            DB::raw('product_items.pos_item_no as pos_item_no'),
            DB::raw('product_items.item_no as item_no'),
            DB::raw('products.product_name as product_name'),
            DB::raw('purchase_detail.item_price as item_price'),
            DB::raw('purchase_detail.item_qty as item_qty'),
            DB::raw('purchase_detail.original_subtotal_price as detail_original_subtotal_price'),
            DB::raw('purchase_detail.subtotal_tax_price as detail_subtotal_tax_price'),
            DB::raw('purchase_detail.subtotal_nontax_price as detail_subtotal_nontax_price'),

        )
            ->Join('purchase_detail', 'purchase_detail.purchase_id', '=', 'purchase.id')
            ->Join('supplier', 'supplier.id', '=', 'purchase.supplier_id')
            ->Join('product_items', 'product_items.id', '=', 'purchase_detail.product_item_id') //產品品項
            ->Join('products', 'products.id', '=', 'product_items.product_id') //產品
            ->leftJoin('order_supplier', 'order_supplier.id', '=', 'purchase.order_supplier_id'); //採購單

        if (isset($in['trade_date_start']) && $in['trade_date_start'] && isset($in['trade_date_end']) && $in['trade_date_end']) { //進貨日期
            $purchase->whereBetween('purchase.trade_date', [$in['trade_date_start'] . ' 00:00:00', $in['trade_date_end'] . ' 23:59:59']);
        }
        if (isset($in['supplier']) && $in['supplier']) { //供應商
            $purchase->where('purchase.supplier_id', $in['supplier']);
        }
        if (isset($in['order_supplier_number']) && $in['order_supplier_number']) { //採購單單號
            $purchase->where('order_supplier.number', $in['order_supplier_number']);
        }
        if (isset($in['POS_start_number']) && $in['POS_start_number'] && isset($in['POS_end_number']) && $in['POS_end_number']) { //POS品號
            $purchase->whereBetween('product_items.pos_item_no', [$in['POS_start_number'], $in['POS_end_number']]);
        }
        if (isset($in['product_name']) && $in['product_name']) { //商品名稱
            $purchase->where('products.product_name', 'like', '%' . $in['product_name'] . '%');
        }
        return $purchase;

    }
}

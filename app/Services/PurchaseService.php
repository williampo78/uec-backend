<?php

namespace App\Services;

use App\Models\Purchase;
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
        return $purchase->get() ; 
    }
    public function getPurchaseDetail()
    {

    }
}

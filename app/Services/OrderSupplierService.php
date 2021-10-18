<?php

namespace App\Services;


use App\Models\OrderSupplier;
use App\Models\OrderSupplierDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderSupplierService
{
    public function __construct()
    {
    }

    public function getOrderSupplier($data){
        $agent_id = Auth::user()->agent_id;

        $result = OrderSupplier::select(DB::raw('order_supplier.id as id') , DB::raw('order_supplier.supplier_id as supplier_id') , DB::raw('order_supplier.number as number'),
                                        DB::raw('requisitions_purchase.number as requisitions_purchase_number') , DB::raw('order_supplier.trade_date as trade_date') , DB::raw('order_supplier.total_price as total_price') , DB::raw('order_supplier.status as status')
                                        )
                                ->where('order_supplier.agent_id' , $agent_id)
                                ->leftJoin('supplier' , 'order_supplier.supplier_id' , '=', 'supplier.id')
                                ->leftJoin('requisitions_purchase' , 'order_supplier.requisitions_purchase_id' , '=' , 'requisitions_purchase.id');

        if (isset($data['supplier'])){
            $result->where('order_supplier.supplier_id' , $data['supplier']);
        }

        if (isset($data['company_number'])){
            $result->where('company_number' , $data['company_number']);
        }

        if (isset($data['status'])){
            $result->where('order_supplier.status' , $data['status']);
        }

        if (isset($data['select_start_date']) && isset($data['select_end_date'])){
            $result->whereBetween('order_supplier.trade_date' , [ $data['select_start_date'] , $data['select_end_date'] ]);
        }

        if (isset($data['order_number'])){
            $result->where('order_supplier.number' , $data['order_number']);
        }

        if (isset($data['requisitions_purchase_number'])){
            $result->where('requisitions_purchase.number' , $data['requisitions_purchase_number']);
        }

        $result = $result->get();

        return $result;
    }

    public function getOrderSupplierById($id){
        return OrderSupplier::select(DB::raw('order_supplier.*') , DB::raw('supplier.name as supplier_name'))
                            ->where('order_supplier.id' , $id)
                            ->leftJoin('supplier' , 'supplier.id' , '=' , 'order_supplier.supplier_id')
                            ->first();
    }

    public function getOrderSupplierDetail($order_supplier_id){
        return OrderSupplierDetail::where('order_supplier_id' , $order_supplier_id)->get();
    }
}

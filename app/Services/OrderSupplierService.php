<?php

namespace App\Services;


use App\Models\OrderSupplier;
use App\Models\OrderSupplierDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use Batch;

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
        return OrderSupplierDetail::select( DB::raw('order_supplier_detail.id as id'),DB::raw('item.name as item_name'), DB::raw('order_supplier_detail.item_unit as item_unit') , DB::raw('order_supplier_detail.item_price as item_price') ,
                                            DB::raw('requisitions_purchase_detail.item_qty as rp_item_qty') , DB::raw('order_supplier_detail.original_subtotal_price as original_subtotal_price') , 'is_giveaway' , DB::raw('order_supplier_detail.item_qty as item_qty'))
                                ->where('order_supplier_id' , $order_supplier_id)
                                ->leftJoin('item' , 'item.id' , '=' , 'order_supplier_detail.item_id')
                                ->leftJoin('requisitions_purchase_detail' , 'order_supplier_detail.requisitions_purchase_id' , '=' , 'requisitions_purchase_detail.requisitions_purchase_id')
                                ->get();
    }

    public function updateOrderSupplier($data){
        $now = Carbon::now();
        $user_id = Auth::user()->id;

        $orderSupplierData = [
            'requisitions_purchase_id' => $data['requisitions_purchase_id'] ,
            'receiver_name' => $data['receiver_name'] ,
            'receiver_address' => $data['receiver_address'] ,
            'invoice_company_number' => $data['invoice_company_number'] ,
            'invoice_name' => $data['invoice_name'] ,
            'invoice_address' => $data['invoice_address'] ,
            'supplier_deliver_date' => $data['supplier_deliver_date'] ,
            'expect_deliver_date' => $data['expect_deliver_date'] ,
            'remark' => $data['remark'] ,
            'status' => $data['status_code'] ,
            'updated_by' => $user_id,
            'updated_at' => $now
        ];

        OrderSupplier::where('id' , $data['id'])->update($orderSupplierData);

        $orderSupplierDetailData = [];

        foreach ($data['order_supplier_detail_id'] as $k => $order_supplier_detail_id){
            $orderSupplierDetailData[$k] = [
                'id' => $order_supplier_detail_id ,
                'is_giveaway' =>  $data['is_giveaway'][$k] ,
                'item_qty' => $data['item_qty'][$k]
            ];
        }

        $orderSupplierDetailInstance = new OrderSupplierDetail();
        Batch::update($orderSupplierDetailInstance,$orderSupplierDetailData,'id');

        return true;
    }
}

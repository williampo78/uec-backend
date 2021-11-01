<?php

namespace App\Services;


use App\Models\OrderSupplier;
use App\Models\OrderSupplierDetail;
use App\Models\RequisitionsPurchaseDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Batch;

class OrderSupplierService
{
    private $universalService;
    private $itemService;
    public function __construct(UniversalService $universalService, ItemService $itemService)
    {
        $this->universalService = $universalService;
        $this->itemService = $itemService;
    }

    public function getOrderSupplier($data){
        $agent_id = Auth::user()->agent_id;

        $result = OrderSupplier::select(DB::raw('order_supplier.id as id') , DB::raw('order_supplier.supplier_id as supplier_id') , DB::raw('order_supplier.number as number'),
                                        DB::raw('requisitions_purchase.number as requisitions_purchase_number') , DB::raw('order_supplier.trade_date as trade_date') , DB::raw('order_supplier.total_price as total_price') , DB::raw('order_supplier.status as status'),
                                        'supplier_deliver_date' , 'expect_deliver_date')
                                ->where('order_supplier.agent_id' , $agent_id)
                                ->leftJoin('supplier' , 'order_supplier.supplier_id' , '=', 'supplier.id')
                                ->leftJoin('requisitions_purchase' , 'order_supplier.requisitions_purchase_id' , '=' , 'requisitions_purchase.id');

        if (isset($data['supplier'])){
            $result->where('order_supplier.supplier_id' , $data['supplier']);
        }

        if (isset($data['company_number'])){
            $result->where('company_number' , 'like' , '%' . $data['company_number'] . '%');
        }

        if (isset($data['status'])){
            $result->where('order_supplier.status' , $data['status']);
        }

        if (isset($data['select_start_date']) && isset($data['select_end_date'])){
            $result->whereBetween('order_supplier.trade_date' , [ $data['select_start_date'] , $data['select_end_date'] ]);
        }

        if (isset($data['order_number'])){
            $result->where('order_supplier.number' , 'like' , '%'. $data['order_number'] .'%');
        }

        if (isset($data['requisitions_purchase_number'])){
            $result->where('requisitions_purchase.number' , 'like' , '%'. $data['requisitions_purchase_number'] . '%');
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
                                            DB::raw('requisitions_purchase_detail.item_qty as rp_item_qty') , DB::raw('order_supplier_detail.original_subtotal_price as original_subtotal_price') , 'is_giveaway' , DB::raw('order_supplier_detail.item_qty as item_qty'),
                                            DB::raw('requisitions_purchase_detail.id as requisitions_purchase_detail_id') , 'purchase_qty' , 'order_supplier_detail.subtotal_price' , 'order_supplier_detail.item_number')
                                ->where('order_supplier_id' , $order_supplier_id)
                                ->leftJoin('item' , 'item.id' , '=' , 'order_supplier_detail.item_id')
                                ->leftJoin('requisitions_purchase_detail' , 'order_supplier_detail.requisitions_purchase_dtl_id' , '=' , 'requisitions_purchase_detail.requisitions_purchase_id')
                                ->get();
    }

    public function updateOrderSupplier($data , $act){
        $now = Carbon::now();
        $user_id = Auth::user()->id;
        $agent_id = Auth::user()->agent_id;
        $orderSupplierData = [
            'trade_date' => $data['trade_date'] ,
            'total_tax_price' => $data['total_tax_price'] ,
            'total_price' => $data['total_price'] ,
            'original_total_tax_price' => $data['original_total_tax_price'] ,
            'original_total_price' => $data['original_total_price'] ,
            'currency_id' => $data['currency_id'] ,
            'currency_code' => $data['currency_code'] ,
            'currency_price' => $data['currency_price'] ,
            'tax' => $data['tax'] ,
            'requisitions_purchase_id' => $data['requisitions_purchase_id'] ,
            'receiver_name' => $data['receiver_name'] ,
            'receiver_address' => $data['receiver_address'] ,
            'invoice_company_number' => $data['invoice_company_number'] ,
            'invoice_name' => $data['invoice_name'] ,
            'invoice_address' => $data['invoice_address'] ,
            'supplier_deliver_date' => $data['supplier_deliver_date'] ,
            'expect_deliver_date' => $data['expect_deliver_date'] ,
            'remark' => $data['remark'] ,
            'status' => 'APPROVED' ,
            'updated_by' => $user_id,
            'updated_at' => $now
        ];

        if ($act == 'add'){
            $orderSupplierData['agent_id'] = $agent_id;
            $orderSupplierData['user_id'] = $user_id;
            $orderSupplierData['created_at'] = $now;
            $orderSupplierData['created_by'] = $user_id;
            $orderSupplierData['supplier_id'] = $data['supplier_id'];
            $orderSupplierData['number'] = $this->universalService->getDocNumber('order_supplier');
            OrderSupplier::insert($orderSupplierData);
        }elseif ($act == 'upd'){
            OrderSupplier::where('id' , $data['id'])->update($orderSupplierData);
        }

        $orderSupplierDetailData = [];
        $requisitionsPurchaseDetailData = [];
        $item = $this->universalService->idtokey($this->itemService->getItemList());

        $order_supplier_detail = json_decode($data['order_supplier_detail_json'],true) ; 
        foreach ($order_supplier_detail as $key => $val){
            $orderSupplierDetailData[$key] = [
                'id' => $val['id'] ,
                'order_supplier_id' => $data['supplier_id'] ,
                'is_giveaway' =>  $val['is_giveaway'] ,
                'item_qty' => $val['item_qty'] ,
                'requisitions_purchase_dtl_id' => $val['requisitions_purchase_dtl_id'] ,
                'item_id' => $val['item_id'] ,
                'item_number' => $val['item_number'] ,
                'item_brand' => $val['item_brand'] ,
                'item_name' => $val['item_name'] ,
                'item_spec' => $val['item_spec'] ,
                'item_lot_number' => 1 ,
                'item_check_qty' => 1 ,
                'item_unit' => 1 ,
                'item_price' => 1 ,
                'subtotal_price' => 1 ,
                'total_price' => 1 ,
                'original_subtotal_price' => 1  ,
                'currency_id' => $data['currency_id'] ,
                'currency_code' => $data['currency_code'] ,
                'currency_price' => $data['currency_price'] ,
                'purchase_qty' => 1
            ];

            // $requisitionsPurchaseDetailData[$key] = [
            //    'id' => $requisitions_purchase_detail_id ,
            //     'is_gift' => $data['is_giveaway'][$k]
            // ] ;

            if ($act=='add'){
                unset($orderSupplierDetailData[$key]['id']);
            }
        }

        $orderSupplierDetailInstance = new OrderSupplierDetail();
        // $requisitionsPurchaseDetailInstance = new RequisitionsPurchaseDetail();
        // Batch::update($requisitionsPurchaseDetailInstance, $requisitionsPurchaseDetailData , 'id');

        if ($act == 'add'){
            $orderSupplierDetailInstance->insert($orderSupplierDetailData);
        }elseif ($act == 'upd'){
            Batch::update($orderSupplierDetailInstance,$orderSupplierDetailData,'id');
        }

        return true;
    }
}

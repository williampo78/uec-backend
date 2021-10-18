<?php

namespace App\Services;



use App\Models\RequisitionsPurchase;
use App\Models\RequisitionsPurchaseDetail;
use App\Models\RequisitionsPurchaseReviewLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RequisitionsPurchaseService
{
    public function __construct()
    {
    }

    public function getRequisitionsPurchase($params)
    {
        $agent_id = Auth::user()->agent_id;

        $select_start_date = '2000-10-10';
        $select_end_date = '2021-10-10';
        $department = 1;
        $active = 1;

        $rs = RequisitionsPurchase::select('requisitions_purchase.*' , DB::RAW('user.name as user_name'), DB::RAW('supplier.name as supplier_name'), DB::RAW('department.name as department_name'), DB::RAW('warehouse.name as warehouse_name'))
                                ->leftJoin('user' , 'requisitions_purchase.user_id' , '=' , 'user.id')
                                ->leftJoin('supplier' , 'requisitions_purchase.supplier_id' , '=' , 'supplier.id')
                                ->leftJoin('department' , 'requisitions_purchase.department_id' , '=' , 'department.id')
                                ->leftJoin('warehouse' , 'requisitions_purchase.warehouse_id', '=' , 'warehouse.id')
                                ->where('requisitions_purchase.agent_id', $agent_id)
                                ->where('trade_date' , '>=' , $select_start_date)
                                ->where('trade_date' , '<=' , $select_end_date)
                                ->where('department_id' , 'like' , $department)
                                ->where('requisitions_purchase.active' , $active)
                                ->orderBy('requisitions_purchase.trade_date' , 'desc')
                                ->orderBy('requisitions_purchase.created_at' , 'desc')
                                ->get();

        return $rs;
    }

    public function getAjaxRequisitionsPurchase($id){
        return RequisitionsPurchase::select(DB::RAW('requisitions_purchase.*') , DB::RAW('supplier.id as supplier_id') , DB::RAW('supplier.name as supplier_name') ,
                                            DB::RAW('supplier.company_number as supplier_company_number'), DB::RAW('warehouse.name as warehouse_name') , DB::RAW('department.name as department_name'))
                                    ->leftJoin('supplier', 'requisitions_purchase.supplier_id' , '=' , 'supplier.id')
                                    ->leftJoin('department' , 'requisitions_purchase.department_id' , '=' , 'department.id')
                                    ->leftJoin('warehouse' , 'requisitions_purchase.warehouse_id' , '=' , 'warehouse.id')
                                    ->where('requisitions_purchase.id' , $id)
                                    ->where('requisitions_purchase.active' , 1)
                                    ->first();
    }

    public function getAjaxRequisitionsPurchaseDetail($id){
        return RequisitionsPurchaseDetail::select(DB::RAW('requisitions_purchase_detail.*'), DB::RAW('item.stock_qty as item_stock_qty'))
                                        ->leftJoin('item' , 'requisitions_purchase_detail.item_id' , '=' , 'item.id' )
                                        ->where('requisitions_purchase_id' , $id)
                                        ->get();
    }

    public function getRequisitionPurchaseById($id){
        return RequisitionsPurchase::where('id' , $id)->get();
    }

    public function getRequisitionPurchaseReviewLog($requisition_purchase_id){
        return RequisitionsPurchaseReviewLog::where('requisitions_purchase_id' , $requisition_purchase_id)
                                            ->leftJoin('users' , 'users.id' , '=' , 'reviewer')
                                            ->get();
    }

    public function getRequisitionPurchaseDetail($requisition_purchase_id){

    }
}

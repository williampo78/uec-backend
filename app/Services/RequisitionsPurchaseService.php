<?php

namespace App\Services;

use App\Models\RequisitionsPurchase;
use App\Models\RequisitionsPurchaseDetail;
use App\Models\RequisitionsPurchaseReviewLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class RequisitionsPurchaseService
{
    private $hierarchyService;
    public function __construct(HierarchyService $hierarchyService)
    {
        $this->hierarchyService = $hierarchyService;
    }

    public function getRequisitionsPurchase($params)
    {
        $agent_id = Auth::user()->agent_id;

        $select_start_date = '2000-10-10';
        $select_end_date = '2021-10-10';
        $department = 1;
        $active = 1;

        $rs = RequisitionsPurchase::select('requisitions_purchase.*', DB::RAW('user.name as user_name'), DB::RAW('supplier.name as supplier_name'), DB::RAW('department.name as department_name'), DB::RAW('warehouse.name as warehouse_name'))
            ->leftJoin('user', 'requisitions_purchase.user_id', '=', 'user.id')
            ->leftJoin('supplier', 'requisitions_purchase.supplier_id', '=', 'supplier.id')
            ->leftJoin('department', 'requisitions_purchase.department_id', '=', 'department.id')
            ->leftJoin('warehouse', 'requisitions_purchase.warehouse_id', '=', 'warehouse.id')
            ->where('requisitions_purchase.agent_id', $agent_id)
            ->where('trade_date', '>=', $select_start_date)
            ->where('trade_date', '<=', $select_end_date)
            ->where('department_id', 'like', $department)
            ->where('requisitions_purchase.active', $active)
            ->orderBy('requisitions_purchase.trade_date', 'desc')
            ->orderBy('requisitions_purchase.created_at', 'desc')
            ->get();

        return $rs;
    }

    public function getAjaxRequisitionsPurchase($id)
    {
        return RequisitionsPurchase::select(DB::RAW('requisitions_purchase.*'), DB::RAW('supplier.id as supplier_id'), DB::RAW('supplier.name as supplier_name'),
            DB::RAW('supplier.company_number as supplier_company_number'), DB::RAW('warehouse.name as warehouse_name'), DB::RAW('department.name as department_name'))
            ->leftJoin('supplier', 'requisitions_purchase.supplier_id', '=', 'supplier.id')
            ->leftJoin('department', 'requisitions_purchase.department_id', '=', 'department.id')
            ->leftJoin('warehouse', 'requisitions_purchase.warehouse_id', '=', 'warehouse.id')
            ->where('requisitions_purchase.id', $id)
            ->where('requisitions_purchase.active', 1)
            ->first();
    }

    public function getAjaxRequisitionsPurchaseDetail($id)
    {
        return RequisitionsPurchaseDetail::select(DB::RAW('requisitions_purchase_detail.*'), DB::RAW('item.stock_qty as item_stock_qty'))
            ->leftJoin('item', 'requisitions_purchase_detail.item_id', '=', 'item.id')
            ->where('requisitions_purchase_id', $id)
            ->get();
    }

    public function getRequisitionPurchaseById($id)
    {
        $agent_id = Auth::user()->agent_id;

        return RequisitionsPurchase::where('agent_id', $agent_id)->where('id', $id)->first();
    }

    public function getRequisitionPurchaseReviewLog($requisition_purchase_id)
    {
        return RequisitionsPurchaseReviewLog::where('requisitions_purchase_id', $requisition_purchase_id)
            ->leftJoin('users', 'users.id', '=', 'reviewer')
            ->get();
    }

    public function getRequisitionPurchaseDetail($requisition_purchase_id)
    {
        return RequisitionsPurchaseDetail::where('requisitions_purchase_id', $requisition_purchase_id)->get();
    }

    public function getRequisitionPurchaseDetailForOrderSupplier($requisition_purchase_id){
        return RequisitionsPurchaseDetail::select(DB::raw('requisitions_purchase_detail.*') , DB::raw('order_supplier_detail.item_qty as order_supplier_qty'))
                                        ->leftJoin('order_supplier_detail', 'order_supplier_detail.requisitions_purchase_dtl_id' , '=' , 'requisitions_purchase_detail.id')
                                        ->where('requisitions_purchase_detail.requisitions_purchase_id', $requisition_purchase_id)
                                        ->get();

    }

    public function getRequisitionsPurchaseReview(){
        $user_id = Auth::user()->id;

        return RequisitionsPurchase::where('status', 'REVIEWING')->where('next_approver', $user_id)->get();
    }

    public function getRequisitionsPurchaseList()
    {
        $agent_id = Auth::user()->agent_id;

        return RequisitionsPurchase::where('agent_id', $agent_id)->get();
    }
    public function createRequisitionsPurchase($input)
    { //建立請購單
        $requisitions_purchase_detail = json_decode($input['requisitions_purchase_detail'], true);
        unset($input['requisitions_purchase_detail']);
        $requisitions_purchase = $input;

        $user_id = Auth::user()->id;
        $now = Carbon::now();
    
        $hierarchy = $this->hierarchyService->getHierarchyCode('QUOTATION');
        dd($hierarchy) ;
        // exit ;
        if (!$hierarchy) {
            return false;
        }

        DB::beginTransaction();
        // try {
        //     $insert = [];
        //     $insert['agent_id'] = Auth::user()->agent_id;
        //     $insert['doc_number'] = $this->universalService->getDocNumber();
        //     $insert['supplier_id'] = $data['supplier_id'];
        //     $insert['status_code'] = $data['status_code'];
        //     $insert['tax'] = $data['tax'];
        //     $insert['remark'] = $data['remark'];
        //     $insert['created_by'] = $user_id;
        //     $insert['created_at'] = $now;
        //     $insert['updated_by'] = $user_id;
        //     $insert['updated_at'] = $now;
        //     $insert['next_approver'] = $hierarchy[0];

        //     if ($data['status_code'] == 'REVIEWING') {
        //         $quotationData['submitted_at'] = $now;
        //     }

        //     $quotation_id = RequisitionsPurchase::insertGetId($quotationData);

        //     $detailData = [];
        //     if (isset($data['item'])) {
        //         foreach ($data['item'] as $k => $item_id) {
        //             $detailData[$k]['item_id'] = $item_id;
        //             $detailData[$k]['quotation_id'] = $quotation_id;
        //             $detailData[$k]['unit_price'] = $data['price'][$k] * 1; //目前匯率皆為1
        //             $detailData[$k]['original_unit_price'] = $data['price'][$k];
        //             $detailData[$k]['created_by'] = $user_id;
        //             $detailData[$k]['created_at'] = $now;
        //             $detailData[$k]['updated_by'] = $user_id;
        //             $detailData[$k]['updated_at'] = $now;
        //         }
        //         RequisitionsPurchase::insert($detailData);
        //     }

        //     //簽核log
        //     foreach ($hierarchy as $seq_no => $reviewer) {
        //         $reviewLogData['quotation_id'] = $quotation_id;
        //         $reviewLogData['created_by'] = $user_id;
        //         $reviewLogData['created_at'] = $now;
        //         $reviewLogData['updated_by'] = $user_id;
        //         $reviewLogData['updated_at'] = $now;
        //         $reviewLogData['seq_no'] = $seq_no + 1;
        //         $reviewLogData['reviewer'] = $reviewer;

        //         QuotationReviewLog::insert($reviewLogData);
        //     }

        //     DB::commit();
        //     $result = true;
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     Log::info($e);

        //     $result = false;
        // }

        // return $result;
    }
}

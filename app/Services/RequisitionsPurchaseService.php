<?php

namespace App\Services;

use App\Models\RequisitionsPurchase;
use App\Models\RequisitionsPurchaseDetail;
use App\Models\RequisitionsPurchaseReviewLog;
use App\Services\UniversalService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RequisitionsPurchaseService
{
    private $hierarchyService;
    private $universalService;
    public function __construct(HierarchyService $hierarchyService, UniversalService $universalService)
    {
        $this->hierarchyService = $hierarchyService;
        $this->universalService = $universalService;
    }

    public function getRequisitionsPurchase($params)
    {
        $agent_id = Auth::user()->agent_id;

        $select_start_date = '2000-10-10';
        $select_end_date = '2021-10-22';
        $department = 1;
        $active = 1;

        $rs = RequisitionsPurchase::select('requisitions_purchase.*', DB::RAW('user.name as user_name'), DB::RAW('supplier.name as supplier_name'),  DB::RAW('warehouse.name as warehouse_name'))
            ->leftJoin('user', 'requisitions_purchase.user_id', '=', 'user.id')
            ->leftJoin('supplier', 'requisitions_purchase.supplier_id', '=', 'supplier.id')
            // ->leftJoin('department', 'requisitions_purchase.department_id', '=', 'department.id')
            ->leftJoin('warehouse', 'requisitions_purchase.warehouse_id', '=', 'warehouse.id')
            ->where('requisitions_purchase.agent_id', $agent_id)
            ->where('trade_date', '>=', $select_start_date)
            ->where('trade_date', '<=', $select_end_date)
            // ->where('department_id', 'like', $department)
            ->where('requisitions_purchase.active', $active)
            ->orderBy('requisitions_purchase.trade_date', 'desc')
            ->orderBy('requisitions_purchase.created_at', 'desc');
        return $rs;
    }

    public function getAjaxRequisitionsPurchase($id)
    {
        return RequisitionsPurchase::select(DB::RAW('requisitions_purchase.*'), DB::RAW('supplier.id as supplier_id'), DB::RAW('supplier.name as supplier_name'),
            DB::RAW('supplier.company_number as supplier_company_number'), DB::RAW('warehouse.name as warehouse_name'))
            ->leftJoin('supplier', 'requisitions_purchase.supplier_id', '=', 'supplier.id')
            // ->leftJoin('department', 'requisitions_purchase.department_id', '=', 'department.id')
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

    public function getRequisitionPurchaseDetailForOrderSupplier($requisition_purchase_id)
    {
        return RequisitionsPurchaseDetail::select(DB::raw('requisitions_purchase_detail.*'), DB::raw('order_supplier_detail.item_qty as order_supplier_qty'))
            ->leftJoin('order_supplier_detail', 'order_supplier_detail.requisitions_purchase_dtl_id', '=', 'requisitions_purchase_detail.id')
            ->where('requisitions_purchase_detail.requisitions_purchase_id', $requisition_purchase_id)
            ->get();

    }

    public function getRequisitionsPurchaseReview()
    {
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
        unset($input['requisitions_purchase_detail']); // 移除json
        unset($input['_token']);
        $requisitions_purchase = $input;

        $user_id = Auth::user()->id;
        $now = Carbon::now();

        $hierarchy = $this->hierarchyService->getHierarchyCode('QUOTATION');

        if (!$hierarchy) {
            return false;
        }
        DB::beginTransaction();
        try {
            //創建主表
            $requisitions_purchase['agent_id'] = $user_id;
            $requisitions_purchase['number'] = $this->universalService->getDocNumber('requisitions_purchase');
            $requisitions_purchase['user_id'] = $user_id;
            $requisitions_purchase['use_date'] = $now; //需用日先填假值
            $requisitions_purchase['created_at'] = $now; //創建時間
            $requisitions_purchase_id = RequisitionsPurchase::insertGetId($requisitions_purchase);
            if (isset($requisitions_purchase_detail)) {
                foreach ($requisitions_purchase_detail as $key => $val) {
                    unset($requisitions_purchase_detail[$key]['id']);
                    $requisitions_purchase_detail[$key]['requisitions_purchase_id'] = $requisitions_purchase_id;
                    $requisitions_purchase_detail[$key]['item_number'] = $requisitions_purchase['number'];
                    $requisitions_purchase_detail[$key]['total_price'] = $requisitions_purchase['total_price'];
                }
                RequisitionsPurchaseDetail::insert($requisitions_purchase_detail);
            }

            //簽核log
            foreach ($hierarchy as $seq_no => $reviewer) {
                $reviewLogData['requisitions_purchase_id'] = $requisitions_purchase_id;
                $reviewLogData['created_by'] = $user_id;
                $reviewLogData['created_at'] = $now;
                $reviewLogData['updated_by'] = $user_id;
                $reviewLogData['updated_at'] = $now;
                $reviewLogData['seq_no'] = $seq_no + 1;
                $reviewLogData['reviewer'] = $reviewer;
                RequisitionsPurchaseReviewLog::insert($reviewLogData);
            }
            DB::commit();
            $result = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e);

            $result = false;
        }

        return $result;
    }
}

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

    public function getRequisitionsPurchase($requset)
    {
        $agent_id = Auth::user()->agent_id;

        $rs = RequisitionsPurchase::select(
            'requisitions_purchase.*',
            DB::RAW('users.user_name as user_name'),
            DB::RAW('supplier.name as supplier_name'),
            DB::RAW('warehouse.name as warehouse_name')
        )
            ->leftJoin('users', 'requisitions_purchase.created_by', '=', 'users.id')
            ->leftJoin('supplier', 'requisitions_purchase.supplier_id', '=', 'supplier.id')
            ->leftJoin('warehouse', 'requisitions_purchase.warehouse_id', '=', 'warehouse.id')
            ->where('requisitions_purchase.agent_id', $agent_id);
        if (!empty($requset['select_start_date'])) {
            $select_start_date = Carbon::parse($requset['select_start_date'])->format('Y-m-d H:i:s');
            $rs->whereDate('trade_date', '>=', $select_start_date);
        }
        if (!empty($requset['select_end_date'])) {
            $select_end_date = Carbon::parse($requset['select_end_date'])->format('Y-m-d H:i:s');
            $rs->whereDate('trade_date', '<=', $select_end_date);
        }
        $rs->orderBy('requisitions_purchase.trade_date', 'desc')
            ->orderBy('requisitions_purchase.created_at', 'desc');

        if (isset($requset['doc_number']) && $requset['doc_number'] !== '') { //請購單編號requisitions_purchase.number
            $rs->where('requisitions_purchase.number', $requset['doc_number']);
        }
        if (isset($requset['supplier_id']) && $requset['supplier_id'] !== '') { //請購單編號  requisitions_purchase.number
            $rs->where('requisitions_purchase.supplier_id', $requset['supplier_id']);
        }
        if (isset($requset['company_number']) && $requset['company_number'] !== '') {
            $rs->where('supplier.company_number', $requset['company_number']);
        }
        if (isset($requset['status']) && $requset['status'] !== '') {
            $rs->where('requisitions_purchase.status', $requset['status']);
        }

        return $rs->get();
    }

    public function getAjaxRequisitionsPurchase($id)
    {
        return RequisitionsPurchase::select(
            DB::RAW('requisitions_purchase.*'),
            DB::RAW('supplier.id as supplier_id'),
            DB::RAW('supplier.name as supplier_name'),
            DB::RAW('supplier.company_number as supplier_company_number'),
            DB::RAW('warehouse.name as warehouse_name')
        )
            ->leftJoin('supplier', 'requisitions_purchase.supplier_id', '=', 'supplier.id')
            ->leftJoin('warehouse', 'requisitions_purchase.warehouse_id', '=', 'warehouse.id')
            ->where('requisitions_purchase.id', $id)
            ->first();
    }

    public function getRequisitionPurchaseDetail($requisitions_purchase_id)
    {
        $result = RequisitionsPurchaseDetail::select(
            DB::raw('requisitions_purchase_detail.*'),
            DB::raw('product_items.product_id as product_id'),
            DB::raw('product_items.spec_1_value as spec_1_value'),
            DB::raw('product_items.spec_2_value as spec_2_value'),
            DB::raw('product_items.pos_item_no as pos_item_no'),
            DB::raw('product_items.ean as ean'),
            DB::raw('products.product_name as product_name'),
            DB::raw('products.uom as uom'),
            DB::raw('products.brand_id as brand_id'),
            DB::raw('product_items.item_no as product_items_no'),
            DB::raw('products.min_purchase_qty as min_purchase_qty'),
        )
            ->where('requisitions_purchase_id', $requisitions_purchase_id)
            ->leftJoin('product_items', 'product_items.id', 'requisitions_purchase_detail.product_item_id')
            ->leftJoin('products', 'products.id', 'product_items.product_id')
            ->get();

        return $result;
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

        return RequisitionsPurchase::where('agent_id', $agent_id)->where('is_transfer', '0')->get();
    }
    public function createRequisitionsPurchase($input)
    { //建立請購單
        $result = [] ;
        $requisitions_purchase_detail = json_decode($input['requisitions_purchase_detail'], true);
        unset($input['requisitions_purchase_detail']); // 移除json
        unset($input['item_price']); // 移除json
        unset($input['_token']);
        unset($input['item_qty']);
        unset($input['old_supplier_id']);
        $requisitions_purchase = $input;
        $user_id = Auth::user()->id;
        $agent_id = Auth::user()->agent_id;
        $now = Carbon::now();
        $hierarchy = $this->hierarchyService->getHierarchyCode('REQUISITION_PUR'); // 取得簽核者
        if (!$hierarchy) {
            $result['message'] = '您未被設定於單據簽核流程中，請聯繫系統管理員';
            $result['status']  = false ; 
            return $result ;
        }
        DB::beginTransaction();
        try {
            //創建主表
            $requisitions_purchase['agent_id'] = $agent_id;
            $requisitions_purchase['number'] = $this->universalService->getDocNumber('requisitions_purchase');
            $requisitions_purchase['created_at'] = $now; //創建時間
            $requisitions_purchase['updated_at'] = $now;
            $requisitions_purchase['created_by'] = $user_id;
            $requisitions_purchase['updated_by'] = $user_id;
            $requisitions_purchase['next_approver'] = $hierarchy[0];
            $requisitions_purchase_id = RequisitionsPurchase::insertGetId($requisitions_purchase);

            if (isset($requisitions_purchase_detail)) {
                foreach ($requisitions_purchase_detail as $key => $val) {
                    unset($requisitions_purchase_detail[$key]['id']);
                    unset($requisitions_purchase_detail[$key]['min_purchase_qty']);
                    unset($requisitions_purchase_detail[$key]['item_uom']);
                    unset($requisitions_purchase_detail[$key]['old_item_price']);
                    $requisitions_purchase_detail[$key]['requisitions_purchase_id'] = $requisitions_purchase_id;
                    $requisitions_purchase_detail[$key]['requisitions_purchase_id'] = $requisitions_purchase_id;
                    $requisitions_purchase_detail[$key]['requisitions_purchase_id'] = $requisitions_purchase_id;
                    $requisitions_purchase_detail[$key]['requisitions_purchase_id'] = $requisitions_purchase_id;
                    $requisitions_purchase_detail[$key]['requisitions_purchase_id'] = $requisitions_purchase_id;
                    $requisitions_purchase_detail[$key]['created_by'] = $user_id;
                    $requisitions_purchase_detail[$key]['created_at'] = $now;
                    $requisitions_purchase_detail[$key]['updated_by'] = $user_id;
                    $requisitions_purchase_detail[$key]['updated_at'] = $now;
                }
                RequisitionsPurchaseDetail::insert($requisitions_purchase_detail);

            }

            if ($requisitions_purchase['status'] == 'REVIEWING') {
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
            }

            DB::commit();
            $result['status'] = true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e);

            $result['status'] = false;
        }

        return $result;
    }
    public function updateRequisitionsPurchase($input)
    { //編輯請購單
        $result = [] ;
        $user_id = Auth::user()->id;
        $now = Carbon::now();
        $requisitions_purchase_detail_in = json_decode($input['requisitions_purchase_detail'], true);

        $hierarchy = $this->hierarchyService->getHierarchyCode('REQUISITION_PUR'); // 取得簽核者
        if (!$hierarchy) {
            $result['message'] = '您未被設定於單據簽核流程中，請聯繫系統管理員';
            $result['status']  = false ; 
            return $result ;
        }
        DB::beginTransaction();
  
        try {
            $requisitions_purchase_in = [
                'supplier_id' => $input['supplier_id'],
                'trade_date' => $input['trade_date'],
                'number' => $input['number'],
                'warehouse_id' => $input['warehouse_id'],
                'currency_code' => $input['currency_code'],
                'currency_price' => $input['currency_price'],
                'original_total_tax_price' => $input['original_total_tax_price'],
                'tax' => $input['tax'],
                'total_tax_price' => $input['total_tax_price'],
                'total_price' => $input['total_price'],
                'remark' => $input['remark'],
                'status' => $input['status'],
                'updated_by' => $user_id,
                'updated_at' => $now,
            ];
            RequisitionsPurchase::where('id', $input['id'])->update($requisitions_purchase_in);
            foreach ($requisitions_purchase_detail_in as $key => $item) {
                $indata = [];
                unset($requisitions_purchase_detail_in[$key]['created_at']);
                unset($requisitions_purchase_detail_in[$key]['updated_at']);
                $indata['product_item_id'] = $item['product_item_id']; //20211221欄位改名為product_item_id
                $indata['item_qty'] = $item['item_qty'];
                $indata['item_price'] = $item['item_price'];
                $indata['item_number'] = $item['item_number'];
                $indata['subtotal_price'] = $item['subtotal_price'];
                $indata['original_subtotal_tax_price'] = $item['original_subtotal_tax_price'];
                $indata['subtotal_tax_price'] = $item['subtotal_tax_price'];
                $indata['currency_code'] = $input['currency_code'];
                $indata['updated_by'] = $user_id;
                $indata['updated_at'] = $now;
                $indata['is_gift'] = $item['is_gift'];
                if ($item['id'] == '') {
                    $indata['created_by'] = $user_id;
                    $indata['created_at'] = $now;
                    RequisitionsPurchaseDetail::insert($indata);
                } else {
                    RequisitionsPurchaseDetail::where('id', $item['id'])->update($indata);
                }
            }
            if ($input['status'] == 'REVIEWING') {
                //簽核log
                foreach ($hierarchy as $seq_no => $reviewer) {
                    $reviewLogData['requisitions_purchase_id'] = $input['id'];
                    $reviewLogData['created_by'] = $user_id;
                    $reviewLogData['created_at'] = $now;
                    $reviewLogData['updated_by'] = $user_id;
                    $reviewLogData['updated_at'] = $now;
                    $reviewLogData['seq_no'] = $seq_no + 1;
                    $reviewLogData['reviewer'] = $reviewer;
                    RequisitionsPurchaseReviewLog::insert($reviewLogData);
                }
            }
            DB::commit();
            $result['status'] = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e);

            $result['status'] = false;
        }
        return $result;
    }
    public function delrequisitionsPurchase($id)
    {
        RequisitionsPurchase::where('id', $id)->delete();
        RequisitionsPurchaseDetail::where('requisitions_purchase_id', $id)->delete();
        RequisitionsPurchaseReviewLog::where('requisitions_purchase_id', $id)->delete();
        return true;
    }
    public function delRequisitionsPurchaseDetail($id)
    {
        RequisitionsPurchaseDetail::where('id', $id)->delete();
        return true;
    }

}

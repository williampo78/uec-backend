<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\QuotationDetails;
use App\Models\QuotationReviewLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class QuotationService
{
    private $universalService;
    private $hierarchyService;
    public function __construct(UniversalService $universalService, HierarchyService $hierarchyService)
    {
        $this->universalService = $universalService;
        $this->hierarchyService = $hierarchyService;
    }

    public function getQuotation($data)
    {
        $agent_id = Auth::user()->agent_id;

        $quotation = Quotation::select('quotation.id', 'trade_date', 'doc_number', 'supplier_id', 'status_code', 'currency_code',
            'exchange_rate', 'quotation.remark', 'submitted_at', 'closed_at', 'tax', 'quotation.created_by', 'supplier.name as supplier_name')
            ->where('quotation.agent_id', $agent_id)
            ->leftJoin('supplier', 'supplier.id', '=', 'quotation.supplier_id');

        if (isset($data['status'])) {
            $quotation->where('status_code', $data['status']);
        }

        if (isset($data['supplier'])) {
            $quotation->where('supplier_id', $data['supplier']);
        }

        if (isset($data['doc_number'])) {
            $quotation->where('doc_number', 'like', '%' . $data['doc_number'] . '%');
        }

        if (isset($data['select_start_date']) && isset($data['select_end_date'])) {
            $quotation->whereBetween('quotation.created_at', [$data['select_start_date'], $data['select_end_date']]);
        }

        if (isset($data['company_number'])) {
            $quotation->where('company_number', $data['company_number']);
        }

        $quotation = $quotation->orderBy('doc_number', 'DESC')->get();

        return $quotation;
    }

    public function getQuotationById($id)
    {
        $agent_id = Auth::user()->agent_id;

        return Quotation::select('quotation.*', 'supplier.name as supplier_name')->where('quotation.agent_id', $agent_id)->where('quotation.id', $id)->leftJoin('supplier', 'supplier.id', '=', 'quotation.supplier_id')->first();
    }

    public function getStatusCode()
    {
        $data = [
            'DRAFTED' => '草稿',
            'REVIEWING' => '簽核中',
            'APPROVED' => '已核准',
            'REJECTED' => '已駁回',
        ];

        return $data;
    }

    public function getTaxList()
    {
        return [
            0 => '未稅',
            1 => '應稅',
            2 => '內含',
            3 => '零稅率',
        ];
    }

    public function addQuotation($data)
    {
        $result = [
            'message' => '' ,
            'status'  => true ,
        ] ;

        $user_id = Auth::user()->id;
        $now = Carbon::now();

        $hierarchy = $this->hierarchyService->getHierarchyCode('QUOTATION');
        if (!$hierarchy) {
            $result['message'] = 'approval_hierarchy table 沒有該使用者';
            $result['status']  = false ; 
            return $result ;
        }
        DB::beginTransaction();
        try {
            $quotationData = [];
            $quotationData['agent_id'] = Auth::user()->agent_id;
            $quotationData['doc_number'] = $this->universalService->getDocNumber('quotation');
            $quotationData['supplier_id'] = $data['supplier_id'];
            $quotationData['status_code'] = $data['status_code'];
            $quotationData['tax'] = $data['tax'];
            $quotationData['remark'] = $data['remark'];
            $quotationData['created_by'] = $user_id;
            $quotationData['created_at'] = $now;
            $quotationData['updated_by'] = $user_id;
            $quotationData['updated_at'] = $now;
            $quotationData['next_approver'] = $hierarchy[0];
            $quotationData['trade_date'] = $data['trade_date'];
            $quotationData['is_tax_included'] = isset($data['is_tax_included']) ? $data['is_tax_included'] : null;

            if ($data['status_code'] == 'REVIEWING') {
                $quotationData['submitted_at'] = $now;
            }
            $quotation_id = Quotation::insertGetId($quotationData);

            $detailData = [];
            if (isset($data['item'])) {
                foreach ($data['item'] as $k => $item_id) {
                    $detailData[$k]['product_item_id'] = $item_id;
                    $detailData[$k]['quotation_id'] = $quotation_id;
                    $detailData[$k]['unit_price'] = round($data['price'][$k] * 1); //目前匯率皆為1
                    $detailData[$k]['original_unit_price'] = $data['price'][$k];
                    $detailData[$k]['created_by'] = $user_id;
                    $detailData[$k]['created_at'] = $now;
                    $detailData[$k]['updated_by'] = $user_id;
                    $detailData[$k]['updated_at'] = $now;
                    //unit_price unit_nontax_price  unit_tax_price
                    if ($data['tax'] == 2) { //應稅內含
                        if ($data['is_tax_included'] == '1') { // 報價含稅
                            $tax_num = round($data['price'][$k] / 1.05, 2);
                            $detailData[$k]['unit_nontax_price'] = round($tax_num); //本幣單價未稅金額
                            $detailData[$k]['unit_tax_price'] = round($data['price'][$k] - $tax_num); //本幣單價稅額
                            $detailData[$k]['original_unit_price'] = $data['price'][$k]; //報價金額 原幣單價(畫面上輸入的單價)
                            $detailData[$k]['original_unit_nontax_price'] = $tax_num; //未稅金額
                            $detailData[$k]['original_unit_tax_price'] = $data['price'][$k] - $tax_num; //稅額
                        } else { //報價不含稅
                            $detailData[$k]['unit_nontax_price'] = round($data['price'][$k]); //本幣單價未稅金額
                            $detailData[$k]['unit_tax_price'] = round((($data['price'][$k] * 1.05) - $data['price'][$k])); //本幣單價稅額
                            $detailData[$k]['original_unit_price'] = $data['price'][$k]; //報價金額 原幣單價(畫面上輸入的單價)
                            $detailData[$k]['original_unit_nontax_price'] = $data['price'][$k]; //未稅金額
                            $detailData[$k]['original_unit_tax_price'] = (($data['price'][$k] * 1.05) - $data['price'][$k]); //稅額
                        }
                    } else { //免稅 零稅率 稅額固定為 0
                        $detailData[$k]['unit_nontax_price'] = $data['price'][$k]; //本幣單價未稅金額
                        $detailData[$k]['unit_tax_price'] = 0; //本幣單價稅額
                        $detailData[$k]['original_unit_price'] = $data['price'][$k]; //報價金額 原幣單價(畫面上輸入的單價)
                        $detailData[$k]['original_unit_nontax_price'] = $data['price'][$k]; //未稅金額
                        $detailData[$k]['original_unit_tax_price'] = 0; //稅額
                    }
                }
                QuotationDetails::insert($detailData);
            }

            //簽核log
            foreach ($hierarchy as $seq_no => $reviewer) {
                $reviewLogData['quotation_id'] = $quotation_id;
                $reviewLogData['created_by'] = $user_id;
                $reviewLogData['created_at'] = $now;
                $reviewLogData['updated_by'] = $user_id;
                $reviewLogData['updated_at'] = $now;
                $reviewLogData['seq_no'] = $seq_no + 1;
                $reviewLogData['reviewer'] = $reviewer;

                QuotationReviewLog::insert($reviewLogData);
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

    public function getQuotationDetail($quotation_id)
    {
        $result = QuotationDetails::select(
            DB::raw('quotation_details.id as quotation_details_id'),
            DB::raw('product_items.product_id as product_id'),
            DB::raw('product_items.ean as ean'),
            DB::raw('products.product_name as product_name'),
            DB::raw('products.brand_id as brand_id'),
            DB::raw('product_items.id as product_items_id'),
            DB::raw('product_items.item_no as product_items_no'),
            DB::raw('product_items.pos_item_no as pos_item_no'),
            DB::raw('product_items.spec_1_value') ,
            DB::raw('product_items.spec_2_value') , 
            DB::raw('quotation_details.original_unit_price as original_unit_price'),
            DB::raw('products.min_purchase_qty as min_purchase_qty'),
        )
            ->where('quotation_id', $quotation_id)
            ->leftJoin('product_items', 'product_items.id', 'quotation_details.product_item_id')
            ->leftJoin('products', 'products.id', 'product_items.product_id')
            ->get();
        return $result;
    }

    public function getQuotationReview()
    {
        $user_id = Auth::user()->id;
        return Quotation::where('status_code', 'REVIEWING')->where('next_approver', $user_id)->get();
    }

    public function getQuotationReviewLog($quotation_id)
    {
        return QuotationReviewLog::where('quotation_id', $quotation_id)
            ->leftJoin('users', 'reviewer', '=', 'users.id')->get();
    }

    public function updateQuotation($data)
    {
        $now = Carbon::now();
        $user_id = Auth::user()->id;
        $quotation_id = $data['id'];
        $quotationData = [
            'supplier_id' => $data['supplier_id'],
            'trade_date' => $data['trade_date'],
            'tax' => $data['tax'],
            'remark' => $data['remark'],
            'status_code' => $data['status_code'],
            'updated_at' => $now,
            'is_tax_included' => isset($data['is_tax_included']) ? $data['is_tax_included'] : null,
        ];
        if ($data['status_code'] == 'REVIEWING') {
            $quotationData['submitted_at'] = $now;
        }
        Quotation::where('id', $quotation_id)->update($quotationData);
        // round
         //unit_price unit_nontax_price  unit_tax_price
        foreach ($data['item'] as $k => $item_id) {
            if ($data['tax'] == 2) { //應稅內含
                if ($data['is_tax_included'] == '1') { // 報價含稅
                    $tax_num = round($data['price'][$k] / 1.05, 2);
                    $unit_nontax_price = round($tax_num); //本幣單價未稅金額
                    $unit_tax_price = round($data['price'][$k] - $tax_num); //本幣單價稅額
                    $original_unit_price = $data['price'][$k]; //報價金額 原幣單價(畫面上輸入的單價)
                    $original_unit_nontax_price = $tax_num; //未稅金額
                    $original_unit_tax_price = $data['price'][$k] - $tax_num; //稅額
                } else { //報價不含稅
                    $unit_nontax_price = round($data['price'][$k]); //本幣單價未稅金額
                    $unit_tax_price = round((($data['price'][$k] * 1.05) - $data['price'][$k])); //本幣單價稅額
                    $original_unit_price = $data['price'][$k]; //報價金額 原幣單價(畫面上輸入的單價)
                    $original_unit_nontax_price = $data['price'][$k]; //未稅金額
                    $original_unit_tax_price = (($data['price'][$k] * 1.05) - $data['price'][$k]); //稅額
                }
            } else { //免稅 零稅率 稅額固定為 0
                $unit_nontax_price = $data['price'][$k]; //本幣單價未稅金額
                $unit_tax_price = 0; //本幣單價稅額
                $original_unit_price = $data['price'][$k]; //報價金額 原幣單價(畫面上輸入的單價)
                $original_unit_nontax_price = $data['price'][$k]; //未稅金額
                $original_unit_tax_price = 0; //稅額
            }

            $quotationDetailData = [
                'product_item_id' => $item_id,
                'unit_price' => round($data['price'][$k]),
                'original_unit_price' => $data['price'][$k],
                'updated_at' => $now,
                'updated_by' => $user_id,
                'unit_nontax_price' => $unit_nontax_price ,
                'unit_tax_price' => $unit_tax_price ,
                'original_unit_price' => $original_unit_price , 
                'original_unit_nontax_price' => $original_unit_nontax_price , 
                'original_unit_tax_price' => $original_unit_tax_price ,
            ];

            if (isset($data['quotation_details_id'][$k])) {
                $quotation_details_id = $data['quotation_details_id'][$k];
                QuotationDetails::where('id', $quotation_details_id)->update($quotationDetailData);
            } else {
                $quotationDetailData['quotation_id'] = $quotation_id;
                $quotationDetailData['created_at'] = $now;
                $quotationDetailData['created_by'] = $user_id;
                QuotationDetails::insert($quotationDetailData);
            }
            $quotationDetailData = [];
        }
        return true;
    }
    public function getItemLastPrice($in)
    {
        $result = Quotation::select(DB::raw('quotation_details.original_unit_price'))
            ->join('quotation_details', 'quotation.id', 'quotation_details.quotation_id')
            ->where('quotation.supplier_id', $in['supplier_id'])
            ->where('quotation.currency_code', $in['currency_code'])
            ->where('quotation.tax', $in['tax'])
            ->where('quotation.status_code', 'APPROVED')
            ->where('quotation_details.product_item_id', $in['product_item_id'])
            ->orderBy('quotation.closed_at')
            ->limit(1)
            ->get();
        return $result;
    }
}

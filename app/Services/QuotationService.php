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

        $quotation = Quotation::select('quotation.id','doc_number','supplier_id','status_code','currency_code',
                                'exchange_rate','quotation.remark','submitted_at' , 'closed_at' , 'tax', 'quotation.created_by')
                            ->where('quotation.agent_id' , $agent_id)
                            ->leftJoin('supplier' , 'supplier.id' , '=' , 'supplier_id');

        if (isset($data['status'])){
            $quotation->where('status_code' , $data['status']);
        }

        if (isset($data['supplier'])){
            $quotation->where('supplier_id' , $data['supplier']);
        }

        if (isset($data['doc_number'])){
            $quotation->where('doc_number' ,'like' , '%'.$data['doc_number'].'%');
        }

        if (isset($data['select_start_date']) && isset($data['select_end_date'])){
            $quotation->whereBetween('quotation.created_at' , [$data['select_start_date'] , $data['select_end_date']]);
        }

        if (isset($data['company_number'])){
            $quotation->where('company_number' , $data['company_number']);
        }

        $quotation = $quotation->orderBy('doc_number' , 'DESC')->get();

        return $quotation;
    }

    public function getQuotationById($id){
        $agent_id = Auth::user()->agent_id;

        return Quotation::select()->where('quotation.agent_id' , $agent_id)->where('id',$id)->first();
    }

    public function getStatusCode(){
        $data =  [
            'DRAFTED' => '草稿' ,
            'REVIEWING' => '簽核中' ,
            'APPROVED' => '已核准' ,
            'REJECTED' => '已駁回'
        ];

        return $data;
    }

    public function getTaxList(){
        return [
            0 => '未稅' ,
            1 => '應稅' ,
            2 => '內含' ,
            3 => '零稅率'
        ];
    }

    public function addQuotation($data){

        $user_id = Auth::user()->id;
//        $user_id = 4;
        $now = Carbon::now();

        $hierarchy = $this->hierarchyService->getHierarchyCode('QUOTATION');
        if (!$hierarchy){
            return false;
        }

        DB::beginTransaction();
        try{
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

            if($data['status_code']=='REVIEWING'){
                $quotationData['submitted_at'] = $now;
            }

            $quotation_id = Quotation::insertGetId($quotationData);

            $detailData = [];
            if (isset($data['item'])) {
                foreach ($data['item'] as $k => $item_id) {
                    $detailData[$k]['item_id'] = $item_id;
                    $detailData[$k]['quotation_id'] = $quotation_id;
                    $detailData[$k]['unit_price'] = $data['price'][$k] * 1; //目前匯率皆為1
                    $detailData[$k]['original_unit_price'] = $data['price'][$k];
                    $detailData[$k]['created_by'] = $user_id;
                    $detailData[$k]['created_at'] = $now;
                    $detailData[$k]['updated_by'] = $user_id;
                    $detailData[$k]['updated_at'] = $now;
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
            $result = true;
        }catch (\Exception $e){
            DB::rollBack();
            Log::info($e);

            $result = false;
        }

        return $result;
    }

    public function getQuotationDetail($quotation_id){
        return QuotationDetails::select(DB::raw('quotation_details.id as quotation_details_id'),DB::raw('item.id as item_id') , DB::raw('item.name as item_name') , DB::raw('item.number as item_number') , 'original_unit_price')
                        ->where('quotation_id' , $quotation_id)
                        ->leftJoin('item' , 'item.id' , 'quotation_details.item_id')
                        ->orderBy('item.id')->get();
    }

    public function getQuotationReview(){
        $user_id = Auth::user()->id;
//        $user_id = 3;

        return Quotation::where('status_code' , 'REVIEWING')->where('next_approver' , $user_id)->get();
    }

    public function getQuotationReviewLog($quotation_id){
        return QuotationReviewLog::where('quotation_id' , $quotation_id)
                                ->leftJoin('users' , 'reviewer' , '=' , 'users.id')->get();
    }

    public function updateQuotation($data){
        $now = Carbon::now();
        $user_id = Auth::user()->id;
        $quotation_id = $data['id'];

        $quotationData = [
            'supplier_id' => $data['supplier_id'] ,
            'submitted_at' => $data['submitted_at'] ,
            'tax' => $data['tax'] ,
            'remark' => $data['remark'] ,
            'status_code' => $data['status_code'] ,
            'updated_at' => $now
        ];

        Quotation::where('id' , $quotation_id)->update($quotationData);

        foreach ($data['item'] as $k => $item_id){
            $quotationDetailData = [
                'item_id' => $item_id ,
                'unit_price' => $data['price'][$k] ,
                'original_unit_price' => $data['price'][$k] ,
                'updated_at' => $now ,
                'updated_by' => $user_id
            ];
            if(isset($data['quotation_details_id'][$k])){
                $quotation_details_id = $data['quotation_details_id'][$k];
                QuotationDetails::where('id' , $quotation_details_id)->update($quotationDetailData);
            }else{
                $quotationDetailData['quotation_id'] = $quotation_id;
                $quotationDetailData['created_at'] = $now;
                $quotationDetailData['created_by'] = $user_id;
                QuotationDetails::insert($quotationDetailData);
            }
            $quotationDetailData = [];
        }
        return true;
    }
    public function getItemLastPrice($in){ //取得報價核准的最後一個金額
        $get =  Quotation::select(DB::raw('quotation_details.original_unit_price'))
        ->join('quotation_details' , 'quotation.id' , 'quotation_details.quotation_id')
        ->where('quotation.supplier_id',$in['supplier_id'])
        ->where('quotation.currency_code',$in['currency_code'])
        ->where('quotation.tax',$in['tax'])
        ->where('quotation.status_code','APPROVED')
        ->where('quotation_details.item_id',$in['item_id']) 
        ->orderBy('quotation.closed_at')
        ->limit(1)
        ->get();
        return $get ;
    }
}

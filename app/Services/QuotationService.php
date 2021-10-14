<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\QuotationDetails;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuotationService
{
    private $universalService;
    public function __construct(UniversalService $universalService)
    {
        $this->universalService = $universalService;
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

        DB::beginTransaction();

        try{
            $quotationData = [];
            $quotationData['agent_id'] = Auth::user()->agent_id;
            $quotationData['doc_number'] = $this->universalService->getDocNumber();
            $quotationData['supplier_id'] = $data['supplier_id'];
            $quotationData['status_code'] = $data['status_code'];
            $quotationData['tax'] = $data['tax'];
            $quotationData['remark'] = $data['remark'];
            $quotationData['created_by'] = Auth::user()->id;
            $quotationData['created_at'] = Carbon::now();
            $quotationData['updated_by'] = Auth::user()->id;
            $quotationData['updated_at'] = Carbon::now();

            if($data['status_code']=='REVIEWING'){
                $quotationData['submitted_at'] = Carbon::now();
            }

            $quotation_id = Quotation::insertGetId($quotationData);

            $detailData = [];
            if (isset($data['item'])) {
                foreach ($data['item'] as $k => $item_id) {
                    $detailData[$k]['item_id'] = $item_id;
                    $detailData[$k]['quotation_id'] = $quotation_id;
                    $detailData[$k]['unit_price'] = $data['price'][$k] * 1; //目前匯率皆為1
                    $detailData[$k]['original_unit_price'] = $data['price'][$k];
                    $detailData[$k]['created_by'] = Auth::user()->id;
                    $detailData[$k]['created_at'] = Carbon::now();
                    $detailData[$k]['updated_by'] = Auth::user()->id;
                    $detailData[$k]['updated_at'] = Carbon::now();
                }
                QuotationDetails::insert($detailData);
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
        return QuotationDetails::select(DB::raw('item.name as item_name') , DB::raw('item.number as item_number') , 'original_unit_price')
                        ->where('quotation_id' , $quotation_id)
                        ->leftJoin('item' , 'item.id' , 'quotation_details.item_id')
                        ->orderBy('item.id')->get();
    }

    public function getReviewService(){
        return Quotation::where('status_code' , 'REVIEWING')->get();
    }
}

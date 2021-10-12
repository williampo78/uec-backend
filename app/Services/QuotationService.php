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

    public function getQuotation()
    {
        $agent_id = Auth::user()->agent_id;
        return Quotation::where('agent_id' , $agent_id)->get();
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

    public function addQuotation($data){

        DB::beginTransaction();

        try{
            $quotationData = [];
            $quotationData['agent_id'] = Auth::user()->agent_id;
            $quotationData['doc_number'] = $this->universalService->getDocNumber();
            $quotationData['supplier_id'] = $data['supplier_id'];
            $quotationData['status_code'] = 'DRAFTED';
            $quotationData['tax'] = $data['tax'];
            $quotationData['created_by'] = Auth::user()->id;
            $quotationData['created_at'] = Carbon::now();
            $quotationData['updated_by'] = Auth::user()->id;
            $quotationData['updated_at'] = Carbon::now();
            $quotation_id = Quotation::insertGetId($quotationData);

            $detailData = [];
            foreach ($data['item'] as $k => $item_id){
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

            DB::commit();
            $result = true;
        }catch (\Exception $e){
            DB::rollBack();
            Log::info($e);

            $result = false;
        }

        return $result;
    }
}

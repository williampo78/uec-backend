<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    private $hierarchyService;
    public function __construct(HierarchyService $hierarchyService)
    {
        $this->hierarchyService = $hierarchyService;
    }

    /**
     * @param $data
     * @param string $type 簽核單類型: 'QUOTATION' => 報價單 , 'REQUISITION_PUR' => 請購單
     * @return bool
     */
    public function updateReview($data , string $type){
        $reviewer = Auth::user()->id;
//        $reviewer = 3;
        $now = Carbon::now();
        $id = $data['id'];
        unset($data['id']);
        $data['review_at'] = $now;
        $data['updated_at'] = $now;
        switch ($type){
            case 'QUOTATION':
                $reviewLogTable = 'quotation_review_log';
                $table = 'quotation';
                $status = 'status_code';
                break;
            case 'REQUISITION_PUR':
                $reviewLogTable = 'requisitions_pur_review_log';
                $table = 'requisitions_purchase';
                $status = 'status';
                break;
        }
        DB::table($reviewLogTable)->where($table.'_id' , $id)->where('reviewer' , $reviewer)->update($data);

        $next_approver = $this->hierarchyService->getNextApproval($type);
        $updateData['updated_at'] = $now;
        if ($data['review_result']==1) {
            if ($next_approver) {
                $updateData['next_approver'] = $next_approver;
            } else {
                $updateData[$status] = 'APPROVED';
                $updateData['closed_at'] = $now;
                $updateData['next_approver'] = null;
            }
        }elseif($data['review_result']==0){
            $updateData[$status] = 'REJECTED';
            $updateData['closed_at'] = $now;
            $updateData['next_approver'] = null;
        }

        if ($type=='REQUISITION_PUR'){
            unset($updateData['closed_at']);
        }

        DB::table($table)->where('id', $id)->update($updateData);

        return true;
    }
}

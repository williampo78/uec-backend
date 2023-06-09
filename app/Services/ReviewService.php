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
        $reviewer = Auth::user()->id; //審核人
        $now = Carbon::now();
        $id = $data['id'];
        unset($data['id']);
        $data['review_at'] = $now;
        $data['updated_at'] = $now;
        //簽核單類型
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
        //更新review_log table
        DB::table($reviewLogTable)->where($table.'_id' , $id)->where('reviewer' , $reviewer)->update($data);
        $next_sql_no  = DB::table($reviewLogTable)->where($table.'_id' , $id)->where('reviewer' , $reviewer)->first()->seq_no += 1 ;  //取得下一個seq_no      
        $next_approver = DB::table($reviewLogTable)->where($table.'_id' , $id)->where('seq_no' , $next_sql_no)->first()->reviewer ?? null; //where seq_no 取得下一個簽核者
        $updateData['updated_at'] = $now;

        //簽核通過
        if ($data['review_result'] == 1) {
            //有下一個簽核者 繼續進入下一關簽核 狀態保持審核中 'REVIEWING'
            if ($next_approver) {
                $updateData['next_approver'] = $next_approver;

            //無下一個簽核者則為簽核最後一關 狀態改為已審核 'APPROVED'
            } else {
                $updateData[$status] = 'APPROVED';
                $updateData['closed_at'] = $now;
                $updateData['next_approver'] = null;
            }

        //簽核未通過
        }elseif($data['review_result']== 0 ){
            // 簽核未通過 狀態改為駁回 'REJECTED' ， 並不再進入下一關簽核，next_approver 改為 null
            $updateData[$status] = 'REJECTED';
            $updateData['closed_at'] = $now;
            $updateData['next_approver'] = null;
        }

        DB::table($table)->where('id', $id)->update($updateData);
        return true;
    }
}

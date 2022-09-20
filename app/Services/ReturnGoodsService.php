<?php

namespace App\Services;

use App\Models\ReturnRequest;
use Illuminate\Support\Facades\DB;

class ReturnGoodsService
{
    private $returnRequestId;
    private $returnRequest;

    /**
     * @param int $id
     * @return $this
     * @Author: Eric
     * @DateTime: 2022/9/20 下午 05:15
     */
    public function setReturnRequestId(int $id)
    {
        $this->returnRequestId = $id;
        return $this;
    }

    /**
     * @return mixed
     * @Author: Eric
     * @DateTime: 2022/9/20 下午 05:15
     */
    public function getReturnRequestId(): int
    {
        return $this->returnRequestId;
    }

    private function getReturnRequest()
    {
        if (empty($this->returnRequest)) {
            $this->returnRequest = ReturnRequest::with(['returnExamination'])
                ->find($this->getReturnRequestId());
        }

        return $this->returnRequest;
    }

    public function handle()
    {
        if (empty($this->getReturnRequest())) {
            return [
                'status'  => false,
                'message' => '退貨申請單不存在'
            ];
        }

        if ($this->getReturnRequest()->returnExamination->whereNull('is_returnable')->isNotEmpty()) {
            return [
                'status'  => false,
                'message' => '退貨檢驗單，尚有未確認的資料'
            ];
        }

        DB::transaction(function () {
            $this->updateReturnRequest();
            $this->updateReturnExamination();
        });
    }

    private function updateReturnRequest()
    {
        $this->getReturnAmountAndPointAndPointDiscount();

        $this->getReturnRequest()
            ->update([
                'status_code' => 'PROCESSING',
                'refund_status' => 'PENDING',
                ''
            ]);
    }

    private function updateReturnExamination()
    {
        $this->getReturnRequest()
            ->returnExamination()
            ->update([
                'status_code' => 'COMPLETED'
            ]);
    }

    private function getReturnAmountAndPointAndPointDiscount()
    {

        return [
            //可退款金額
            'returnable_amount' => $this->getReturnRequest()->returnExamination->sum('returnable_amount'),
            //可歸還點數
            'returnable_points' => $this->getReturnRequest()->returnExamination->sum('returnable_points'),
            //可歸還點數價值
            'returnable_point_discount' => $this->getReturnRequest()->returnExamination->sum('returnable_point_discount'),
        ];
    }

}

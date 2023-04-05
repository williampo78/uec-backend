<?php

namespace App\Services;

use App\Models\ShoppingCartErrorLog;
use Illuminate\Support\Facades\Auth;

class ShoppingCartErrorLogService
{
    private ShoppingCartErrorLog $model;
    public function __construct(ShoppingCartErrorLog $shoppingCartErrorLog)
    {
        $this->model = $shoppingCartErrorLog;
    }

    /**
     * 寫入錯誤記錄，包含：program_code, member_id, member_account, order_no, log_type, error_log
     * @param $program_code
     * @param $member_id
     * @param $member_account
     * @param $order_no
     * @param $log_type
     * @param $error_log
     *
     * @return mixed
     */
    public function writeErrorLog($program_code, $member_id, $member_account, $order_no, $log_type, $error_log)
    {
        return $this->model->create([
            'program_code'  => $program_code ?? null,
            'member_id'     => $member_id ?? null,
            'member_account'=> $member_account ?? null,
            'order_no'      => $order_no ?? null,
            'log_type'      => $log_type ?? null,
            'error_log'     => $error_log ?? null,
            'created_by'    => Auth::user()->id ?? -1,
            'updated_by'    => Auth::user()->id ?? -1,
         ]);
    }
}

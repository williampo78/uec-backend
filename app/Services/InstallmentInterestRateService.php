<?php

namespace App\Services;

use App\Models\InstallmentInterestRate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class InstallmentInterestRateService
{
    /**
     * @param array $payload
     * @return Model|null
     * @Author: Eric
     * @DateTime: 2022/8/16 下午 03:46
     */
    public function getEnableInstallmentInterestRate($payload = []): ?Model
    {
        return InstallmentInterestRate::where('active', 1)
            ->where('issuing_bank_no', $payload['issuing_bank_no'])
            ->where('number_of_installments', $payload['number_of_installments'])
            ->whereDate('started_at', '<=', $payload['ended_at'])
            ->whereDate('ended_at', '>=', $payload['started_at'])
            ->when(!empty($payload['exclude_id']), function ($query) use ($payload) {
                $query->where('id', '<>', $payload['exclude_id']);
            })
            ->first();
    }

    /**
     * @param array $payload
     * @return Collection
     * @Author: Eric
     * @DateTime: 2022/8/9 下午 05:19
     */
    public function getList($payload = []): Collection
    {
        //分期資料
        $installmentInterestRates = InstallmentInterestRate::with(['bank:id,bank_no,short_name', 'updatedBy:id,user_name'])
            ->when(isset($payload['bank_id']), function ($query) use ($payload) {
                $query->whereHas('bank', function ($query) use ($payload) {
                    $query->where('id', $payload['bank_id']);
                });
            })
            ->when(isset($payload['number_of_installments']), function ($query) use ($payload) {
                $query->where('number_of_installments', $payload['number_of_installments']);
            })
            ->when(isset($payload['status']), function ($query) use ($payload) {

                [$active, $operate] = explode('-', $payload['status']);

                $query->when(isset($active), function ($query) use ($active) {
                    $query->where('active', $active);
                });
                //適用期間
                $query->when(isset($operate), function ($query) use ($operate) {
                    $today = Carbon::today()->toDateString();
                    switch ($operate) {
                        case 'less_than':
                            $query->whereDate('started_at', '>', $today);
                            break;
                        case 'equal':
                            $query->whereDate('started_at', '<=', $today)
                                ->whereDate('ended_at', '>=', $today);
                            break;
                        case 'more_than':
                            $query->whereDate('ended_at', '<', $today);
                            break;
                    }
                });
            })
            ->orderBy('issuing_bank_no')
            ->orderBy('started_at', 'desc')
            ->orderBy('ended_at', 'desc')
            ->orderBy('number_of_installments')
            ->get(['id', 'issuing_bank_no', 'number_of_installments', 'started_at', 'ended_at', 'interest_rate', 'min_consumption', 'active', 'remark', 'updated_by', 'updated_at']);

        return $installmentInterestRates;
    }

    /**
     * @param Collection $list
     * @return Collection
     * @Author: Eric
     * @DateTime: 2022/8/9 下午 03:28
     */
    public function handleList(Collection $list): Collection
    {
        $nowTimestamp = now()->timestamp;

        return $list->map(function ($installmentInterestRate) use ($nowTimestamp) {

            $activeChinese = '已上架';

            if ($installmentInterestRate->active == 0) {
                $activeChinese = '關閉';
            } else if (strtotime($installmentInterestRate->started_at) > $nowTimestamp) {
                $activeChinese = '待上架';
            } else if (strtotime($installmentInterestRate->ended_at) < $nowTimestamp) {
                $activeChinese = '下架';
            }

            return [
                'id'                     => $installmentInterestRate->id,
                'bank_code_and_name'     => sprintf('%s %s', $installmentInterestRate->issuing_bank_no, $installmentInterestRate->bank->short_name),
                'period'                 => sprintf('%s ~ %s', Carbon::parse($installmentInterestRate->started_at)->toDateString(), Carbon::parse($installmentInterestRate->ended_at)->toDateString()),
                'number_of_installments' => $installmentInterestRate->number_of_installments,
                'interest_rate'          => $installmentInterestRate->interest_rate,
                'min_consumption'        => number_format($installmentInterestRate->min_consumption),
                'active_chinese'         => $activeChinese,
                'remark'                 => $installmentInterestRate->remark,
                'updated_at'             => $installmentInterestRate->updated_at->toDateTimeString(),
                'updated_by'             => optional($installmentInterestRate->updatedBy)->user_name,
            ];
        });
    }
}

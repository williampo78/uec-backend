<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\InstallmentInterestRate;
use App\Services\InstallmentInterestRateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InstallmentInterestRateController extends Controller
{
    private $installmentInterestRateService;

    public function __construct(InstallmentInterestRateService $installmentInterestRateService)
    {
        $this->installmentInterestRateService = $installmentInterestRateService;
    }

    /**
     * @param Request $request
     * @return View
     * @Author: Eric
     * @DateTime: 2022/8/9 下午 03:17
     */
    public function index(Request $request): View
    {
        $installmentInterestRates = collect();

        //權限判斷
        if ($request->share_role_auth['auth_query']) {

            $payload = $request->only(
                [
                    'bank_no',
                    'number_of_installments',
                    'status',
                ]
            );

            $installmentInterestRates = $this->installmentInterestRateService->getList($payload);
            $installmentInterestRates = $this->installmentInterestRateService->handleList($installmentInterestRates);
        }

        $banks = Bank::where('active', 1)
            ->orderBy('bank_no', 'asc')
            ->get(['id', 'bank_no', 'short_name']);

        $banks = $banks->map(function ($bank) {

            return [
                'id'   => $bank->bank_no,
                'text' => sprintf('%s %s', $bank->bank_no, $bank->short_name),
            ];
        });

        $periods = [3, 6, 9, 12, 18, 24, 30];
        //期數的options
        $numberOfInstallments = array_reduce($periods, function ($carry, $item) {

            $carry[] = [
                'id'   => $item,
                'text' => $item,
            ];

            return $carry;
        }, []);

        $parameters                             = [];
        $parameters['banks']                    = $banks;
        $parameters['numberOfInstallments']     = $numberOfInstallments;
        $parameters['installmentInterestRates'] = $installmentInterestRates;
        $parameters['statuses']                 = [
            [
                'id'   => '1-less_than',
                'text' => '待上架',
            ],
            [
                'id'   => '1-equal',
                'text' => '已上架',
            ],
            [
                'id'   => '1-more_than',
                'text' => '下架',
            ],
            [
                'id'   => '0-',
                'text' => '關閉',
            ],
        ];

        return view('backend.installment_interest_rates.list', $parameters);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Author: Eric
     * @DateTime: 2022/8/11 上午 11:45
     */
    public function store(Request $request): JsonResponse
    {
        if ($request->share_role_auth['auth_create'] != 1) {
            return response('Forbidden', 403);
        }

        $payload = $request->only(
            [
                'issuing_bank_no',
                'number_of_installments',
                'started_at',
                'ended_at',
                'interest_rate',
                'min_consumption',
                'active',
                'remark'
            ]
        );

        $validator = \Validator::make($payload,
            [
                'issuing_bank_no'        => 'required',
                'number_of_installments' => 'required|integer',
                'started_at'             => 'required|date|after_or_equal:today',
                'ended_at'               => 'required|date|after_or_equal:started_at',
                'interest_rate'          => 'required|numeric|between:0,99.99',
                'min_consumption'        => 'required|integer',
                'active'                 => 'required|integer',
                'remark'                 => 'max:50'
            ], [
                'required'              => '請輸入:attribute 名稱',
                'integer'               => '請輸入整數',
                'date'                  => ':attribute日期格式錯誤',
                'before_or_equal'       => '適用日期錯誤',
                'after_or_equal'        => '適用日期錯誤',
                'interest_rate.between' => '僅可輸入0或小於100的數字，最多輸入二位小數。',
                'max'                   => ':attribute請勿超過:max字',
            ]);

        if ($validator->fails()) {

            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        //如果是啟用，要判斷此筆是否被使用
        if ($payload['active'] == 1) {
            $installmentInterestRate = $this->installmentInterestRateService->getEnableInstallmentInterestRate($payload);
            if (!empty($installmentInterestRate)) {
                return response()->json([
                    'status'  => false,
                    'message' => '同時間有相同的設定',
                ]);
            }
        }

        $user_id = Auth()->id();

        $installmentInterestRate = InstallmentInterestRate::create([
            'issuing_bank_no'        => $payload['issuing_bank_no'],
            'number_of_installments' => $payload['number_of_installments'],
            'started_at'             => date('Y-m-d 00:00:00', strtotime($payload['started_at'])),
            'ended_at'               => date('Y-m-d 23:59:59', strtotime($payload['ended_at'])),
            'interest_rate'          => $payload['interest_rate'],
            'min_consumption'        => $payload['min_consumption'],
            'active'                 => $payload['active'],
            'remark'                 => $payload['remark'],
            'created_by'             => $user_id,
            'updated_by'             => $user_id,
        ]);

        return response()->json([
            'status'  => true,
            'message' => '新增成功',
            //'data'    => $this->installmentInterestRateService->handleSingle($installmentInterestRate)
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Author: Eric
     * @DateTime: 2022/8/16 下午 05:34
     */
    public function update(Request $request): JsonResponse
    {
        //權限判斷
        if ($request->share_role_auth['auth_update'] != 1) {
            return response()->json([
                'status'  => false,
                'message' => 'Forbidden 403',
            ]);
        }

        //必要的參數
        $payload = $request->only(
            [
                'id',
                'active',
                'remark'
            ]
        );

        //驗證規則
        $rules = [
            'id'     => 'required',
            'active' => 'required|integer',
            'remark' => 'max:50'
        ];

        $installmentInterestRate = InstallmentInterestRate::findOrFail($payload['id']);

        //用來確認資料，是否存在的query資料
        $checkExistedParams = [
            'issuing_bank_no'        => $installmentInterestRate->issuing_bank_no,
            'number_of_installments' => $installmentInterestRate->number_of_installments,
            'started_at'             => $installmentInterestRate->started_at,
            'ended_at'               => $installmentInterestRate->ended_at,
            'exclude_id'             => $installmentInterestRate->id,
        ];

        //狀態為啟用
        if ($installmentInterestRate->active == 1) {

            $nowTimestamp = now()->timestamp;
            //根據狀態,判斷能更新那些欄位
            //待上架
            if (strtotime($installmentInterestRate->started_at) > $nowTimestamp) {
                //更新資料的參數
                $payload['started_at']      = date('Y-m-d 00:00:00', strtotime($request->started_at));
                $payload['ended_at']        = date('Y-m-d 23:59:59', strtotime($request->ended_at));
                $payload['interest_rate']   = $request->interest_rate;
                $payload['min_consumption'] = $request->min_consumption;
                //增加驗證規則
                $rules['started_at']      = 'required|date|after_or_equal:today';
                $rules['ended_at']        = 'required|date|after_or_equal:started_at';
                $rules['interest_rate']   = 'required|numeric|between:0,99.99';
                $rules['min_consumption'] = 'required|integer';
                //增加確認資料是否存在的資料
                $checkExistedParams['started_at'] = date('Y-m-d 00:00:00', strtotime($payload['started_at']));
                $checkExistedParams['ended_at']   = date('Y-m-d 23:59:59', strtotime($payload['ended_at']));

                //已上架
            } else if (strtotime($installmentInterestRate->started_at) <= $nowTimestamp && strtotime($installmentInterestRate->ended_at) >= $nowTimestamp) {
                //更新資料的參數
                $payload['ended_at'] = date('Y-m-d 23:59:59', strtotime($request->ended_at));

                //增加驗證規則
                $rules['ended_at'] = 'required|date|after_or_equal:today';
                //增加確認資料是否存在的資料
                $checkExistedParams['ended_at']   = $payload['ended_at'];
            }
        }

        $validator = \Validator::make($payload,
            $rules, [
                'required'              => '請輸入:attribute 名稱',
                'integer'               => '請輸入整數',
                'date'                  => ':attribute日期格式錯誤',
                'before_or_equal'       => '適用日期錯誤',
                'after_or_equal'        => '適用日期錯誤',
                'interest_rate.between' => '僅可輸入0或小於100的數字，最多輸入二位小數。',
                'max'                   => ':attribute請勿超過:max字',
            ]);

        if ($validator->fails()) {

            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        //更新為啟用前，確認是否有相同的設定資料存在
        if ($payload['active'] == 1) {
            $enableInstallmentInterestRate = $this->installmentInterestRateService->getEnableInstallmentInterestRate($checkExistedParams);

            if (!empty($enableInstallmentInterestRate)) {
                return response()->json([
                    'status'  => false,
                    'message' => '請確認發卡銀行、期數，<br>同時間只能有一組啟用設定',
                ]);
            }
        }

        $installmentInterestRate->update($payload);

        return response()->json([
            'status'  => true,
            'message' => '更新成功',
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Author: Eric
     * @DateTime: 2022/8/12 上午 10:27
     */
    public function checkExisted(Request $request): JsonResponse
    {
        $payload = $request->only(
            [
                'issuing_bank_no',
                'number_of_installments',
                'started_at',
                'ended_at',
                'exclude_id'
            ]
        );

        $validator = \Validator::make($payload,
            [
                'issuing_bank_no'        => 'required',
                'number_of_installments' => 'required|integer',
                'started_at'             => 'required|date|before_or_equal:ended_at',
                'ended_at'               => 'required|date|after_or_equal:started_at',
            ], [
                'required'        => '請輸入:attribute 名稱',
                'integer'         => '請輸入整數',
                'date'            => ':attribute日期格式錯誤',
                'before_or_equal' => '適用日期錯誤',
                'after_or_equal'  => '適用日期錯誤',
            ]);

        if ($validator->fails()) {

            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        $installmentInterestRate = $this->installmentInterestRateService->getEnableInstallmentInterestRate($payload);
        if (!empty($installmentInterestRate)) {
            return response()->json([
                'status'  => false,
                'message' => '請確認發卡銀行、期數，<br>同時間只能有一組啟用設定',
            ]);
        }

        return response()->json([
            'status'  => true,
            'message' => 'ok',
        ]);
    }

    /**
     * @param int $id
     * @return JsonResponse
     * @Author: Eric
     * @DateTime: 2022/8/12 下午 05:06
     */
    public function show(Request $request, int $id): JsonResponse
    {
        if ($request->share_role_auth['auth_query'] != 1) {
            return response()->json([
                'status'  => false,
                'message' => 'Forbidden 403',
            ]);
        }

        $installmentInterestRate = InstallmentInterestRate::find($id, [
            'id',
            'issuing_bank_no',
            'number_of_installments',
            'started_at',
            'ended_at',
            'interest_rate',
            'min_consumption',
            'active',
            'remark'
        ]);

        return response()->json([
            'status' => !empty($installmentInterestRate),
            'data'   => $installmentInterestRate
        ]);
    }
}

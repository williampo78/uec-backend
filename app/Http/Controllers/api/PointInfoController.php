<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\APIService;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;

class PointInfoController extends Controller
{

    public function __construct(APIService $apiService)
    {
        $this->apiService = $apiService;
    }

    /*
     * 查詢可用點數歷程 (會員編號)
     * @param  int  $id
     */
    public function point()
    {
        $member_id = Auth::guard('api')->user()->member_id;
        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $response = $this->apiService->getPointInfo($member_id);
        $result = json_decode($response, true);
        try {
            if ($result['status'] == '200') {
                return response()->json(['status' => true, 'error_code' => $err, 'error_msg' => null, 'result' => $result['data']]);
            } else {
                $err = $result['status'];
                return response()->json(['status' => false, 'error_code' => $err, 'error_msg' => $result['message'], 'result' => (isset($result['error'])?$result['error']:[])]);
            }
        } catch (JWTException $e) {
            Log::info($e);
            $err = '404';
            return response()->json(['status' => false, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => []]);
        }
    }


    /*
     * 查詢即將到期點數歷程 (會員編號)
     * @param  int  $id
     */
    public function expiringPoint()
    {
        $member_id = Auth::guard('api')->user()->member_id;
        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $response = $this->apiService->getExpiringPointInfo($member_id);
        $result = json_decode($response, true);
        try {
            if ($result['status'] == '200') {
                return response()->json(['status' => true, 'error_code' => $err, 'error_msg' => null, 'result' => $result['data']]);
            } else {
                $err = $result['status'];
                return response()->json(['status' => false, 'error_code' => $err, 'error_msg' => $result['message'], 'result' => (isset($result['error'])?$result['error']:[])]);
            }
        } catch (JWTException $e) {
            Log::info($e);
            $err = '404';
            return response()->json(['status' => false, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => []]);
        }
    }
}

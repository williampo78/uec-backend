<?php

namespace App\Services;

use App\Models\AgentConfig;
use App\Models\Members;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Log;
class AuthService
{
    public function __construct(APIService $apiService)
    {
        $this->apiService = $apiService;
    }

    /*
     * 驗證
     */
    public function login ($input){

        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $input['channel'] = "EC";
        $fields = json_encode($input);
        $response = $this->apiService->memberLogin($fields);
        $result = json_decode($response, true);

        unset($input['password']);
        try {
            if ($result['status'] == '200') {
                $status = true;
                $tmp = Members::where('member_id', '=', $result['data']['id'])->first();
                if (!is_null($tmp)) {
                    $token = JWTAuth::fromSubject($tmp);
                    Members::where('id', $tmp['id'])->update(['api_token' => $token]);
                } else {
                    $credentials['member_id'] = $result['data']['id'];
                    $member = Members::create($credentials);
                    $token = JWTAuth::fromSubject($member);
                    Members::where('member_id', '=', $result['data']['id'])->update(['api_token' => $token]);
                }
                unset($result['data']['id']);
                unset($result['data']['recommendSource']);
                $result['data']['_token'] = $token;
                return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $result['data']]);
            } else {
                $status = false;
                $err = $result['status'];
                $result['data'] = [];
                return response()->json(['status' => false, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => []]);
            }
        } catch (JWTException $e) {
            Log::info($e);
            $err = '404';
            return response()->json(['status' => false, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => []]);
        }
    }

}

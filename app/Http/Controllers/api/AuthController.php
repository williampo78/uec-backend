<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\Fluent\Concerns\Has;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use JWTFactory;
use App\Models\Members;
use App\Services\APIService;
use Log;
use Illuminate\Support\Facades\Config;
use Validator;

class AuthController extends Controller
{
    public function __construct(APIService $apiService)
    {
        $this->member = new Members;
        $this->apiService = $apiService;
        //$this->middleware('jwt.auth', ['except' => ['login']]);
    }

    public function login()
    {

        $err = null;
        $credentials = request(['mobile', 'password']);

        $messages = [
            'mobile.required' => '帳號不能為空',
            'password.required' => '密碼不能為空',
        ];

        $v = Validator::make($credentials, [
            'mobile' => 'required',
            'password' => 'required',
        ], $messages);

        if ($v->fails()) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => '資料錯誤', 'result' => $v->errors()]);
        }

        $credentials['channel'] = "EC";
        $fields = json_encode($credentials);
        $response = $this->apiService->memberLogin($fields);
        $result = json_decode($response, true);
        $error_code = $this->apiService->getErrorCode();
        unset($credentials['mobile']);
        unset($credentials['password']);
        unset($credentials['channel']);
        try {
            if ($result['status'] == '200') {
                $status = true;
                $tmp = Members::where('member_id', '=', $result['data']['id'])->first();
                if (!is_null($tmp)) {
                    //$token = JWTAuth::fromSubject($tmp);
                    $token = Auth::guard('api')->login($tmp);
                    Members::where('id', $tmp['id'])->update(['api_token' => $token]);
                } else {
                    $credentials['member_id'] = $result['data']['id'];
                    $member = Members::create($credentials);
                    //$token = JWTAuth::fromSubject($member);
                    $token = Auth::guard('api')->login($member);
                    Members::where('member_id', '=', $result['data']['id'])->update(['api_token' => $token]);
                }
                unset($result['data']['id']);
                unset($result['data']['recommendSource']);
                $result['data']['_token'] = $token;
                return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $result['data']]);
            } else {
                $status = false;
                $err = $result['status'];
                return response()->json(['status' => false, 'error_code' => $err, 'error_msg' => $result['message'], 'result' => (isset($result['error']) ? $result['error'] : [])]);
            }
        } catch (JWTException $e) {
            Log::info($e);
            $err = '404';
            return response()->json(['status' => false, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => []]);
        }

    }

    public function me()
    {
        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $token = JWTAuth::getToken();
        $member = Members::where('api_token', '=', $token)->get();
        if ($member) {
            $message = 'token有效';
            $status = true;
        } else {
            $message = 'token無效';
            $status = false;
            $err = '203';
        }

        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => ['message' => $message]]);
    }

    public function logout(Request $request)
    {

        /*
        $token = JWTAuth::getToken();
        //dd($token);
        $member = Members::where('api_token', '=', $token)->get();
        if (JWTAuth::parseToken()->invalidate()) {
            $message = 'token已失效';
            Members::where('id', $member[0]['id'])->update(['api_token' => '']);
            //Members::where('id', '=', $member[0]['id'])->delete();
        }
        */
        Auth::guard('api')->logout();

        $message = '登出成功';
        return response()->json(['status' => true, 'error_code' => null, 'error_msg' => null, 'result' => $message]);

    }

    public function getMemberStatus(Request $request)
    {
        $credentials = request(['mobile']);

        $messages = [
            'mobile.required' => '手機不能為空',
        ];

        $v = Validator::make($credentials, [
            'mobile' => 'required',
        ], $messages);

        if ($v->fails()) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => '資料錯誤', 'result' => $v->errors()]);
        }
        $err = null;
        $error_code = $this->apiService->getErrorCode();

        $response = $this->apiService->getMemberSMSStatus($request['mobile']);
        $result = json_decode($response, true);
        try {
            if ($result['status'] == '200') {
                return response()->json(['status' => true, 'error_code' => null, 'message' => $result['message'], 'result' => $result['data']]);
            } else {
                $err = $result['status'];
                return response()->json(['status' => false, 'error_code' => $err, 'error_msg' => $result['message'], 'result' => (isset($result['error']) ? $result['error'] : [])]);
            }
        } catch (JWTException $e) {
            Log::info($e);
            $err = '404';
            return response()->json(['status' => false, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => []]);
        }

    }

    public function sendSms(Request $request)
    {
        $credentials = request(['mobile']);

        $messages = [
            'mobile.required' => '手機不能為空',
        ];

        $v = Validator::make($credentials, [
            'mobile' => 'required',
        ], $messages);

        if ($v->fails()) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => '資料錯誤', 'result' => $v->errors()]);
        }
        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $data = [];
        $data['mobile'] = $request['mobile'];
        $response = $this->apiService->sendMemberSMS($data);
        $result = json_decode($response, true);
        try {
            if ($result['status'] == '200') {
                return response()->json(['status' => true, 'error_code' => null, 'message' => $result['message'], 'result' => []]);
            } else {
                $err = $result['status'];
                return response()->json(['status' => false, 'error_code' => $err, 'error_msg' => $result['message'], 'result' => (isset($result['error']) ? $result['error'] : [])]);
            }
        } catch (JWTException $e) {
            Log::info($e);
            $err = '404';
            return response()->json(['status' => false, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => []]);
        }

    }

    public function verifySMS(Request $request)
    {
        $credentials = request(['mobile', 'code']);

        $messages = [
            'mobile.required' => '手機不能為空',
            'code.required' => '驗證碼不能為空',
        ];

        $v = Validator::make($credentials, [
            'mobile' => 'required',
            'code' => 'required',
        ], $messages);

        if ($v->fails()) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => '資料錯誤', 'result' => $v->errors()]);
        }
        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $data = [];
        $data['mobile'] = $request['mobile'];
        $data['code'] = $request['code'];
        $response = $this->apiService->verifyMemberSMS($data);
        $result = json_decode($response, true);
        try {
            if ($result['status'] == '200') {
                return response()->json(['status' => true, 'error_code' => null, 'message' => $result['message'], 'result' => $result['data']]);
            } else {
                $err = $result['status'];
                return response()->json(['status' => false, 'error_code' => $err, 'error_msg' => $result['message'], 'result' => (isset($result['error']) ? $result['error'] : [])]);
            }
        } catch (JWTException $e) {
            Log::info($e);
            $err = '404';
            return response()->json(['status' => false, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => []]);
        }

    }

    public function registration(Request $request)
    {
        $credentials = request(['mobile', 'name', 'email', 'password', 'birthday', 'sex', 'registeredSource']);

        $messages = [
            'mobile.required' => '帳號不能為空',
            'name.required' => '姓名不能為空',
            'password.required' => '密碼不能為空',
            'birthday.required' => '生日不能為空',
            'sex.required' => '性別不能為空',
            'registeredSource.required' => '註冊來源不能為空',
        ];

        $v = Validator::make($credentials, [
            'mobile' => 'required',
            'name' => 'required',
            'password' => 'required',
            'birthday' => 'required',
            'sex' => 'required',
            'registeredSource' => 'required',
        ], $messages);

        if ($v->fails()) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => '資料錯誤', 'result' => $v->errors()]);
        }
        $err = null;
        $error_code = $this->apiService->getErrorCode();

        $data = [];
        $data['mobile'] = $request['mobile'];
        $data['name'] = $request['name'];
        $data['password'] = $request['password'];
        $data['email'] = $request['email'];
        $data['birthday'] = $request['birthday'];
        $data['sex'] = $request['sex'];
        $data['registeredSource'] = $request['registeredSource'];
        $token = $request->server->getHeaders()['AUTHORIZATION'];
        $response = $this->apiService->memberRegistration($data, $token);
        $result = json_decode($response, true);
        try {
            if ($result['status'] == '201') {
                $login['mobile'] = $data['mobile'];
                $login['password'] = $data['password'];
                $login['channel'] = "EC";
                $fields = json_encode($login);
                $response = $this->apiService->memberLogin($fields);
                $login_result = json_decode($response, true);
                unset($login['mobile']);
                unset($login['password']);
                if ($login_result['status'] == '200') {
                    $tmp = Members::where('member_id', '=', $login_result['data']['id'])->first();
                    if (!is_null($tmp)) {
                        $token = Auth::guard('api')->fromUser($tmp);
                        Members::where('id', $tmp['id'])->update(['api_token' => $token]);
                    } else {
                        $login['member_id'] = $login_result['data']['id'];
                        $member = Members::create($login);
                        $token = Auth::guard('api')->fromUser($member);
                        Members::where('member_id', '=', $login_result['data']['id'])->update(['api_token' => $token]);
                    }
                }
                $result['data']['_token'] = $token;
                return response()->json(['status' => true, 'error_code' => null, 'message' => '新增成功', 'result' => $result['data']]);
            } else {
                $err = $result['status'];
                return response()->json(['status' => false, 'error_code' => $err, 'error_msg' => $result['message'], 'result' => (isset($result['error']) ? $result['error'] : [])]);
            }
        } catch (JWTException $e) {
            Log::info($e);
            $err = '404';
            return response()->json(['status' => false, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => []]);
        }
    }

    public function memberBasic(Request $request)
    {
        $credentials = request(['mobile']);

        $messages = [
            'mobile.required' => '手機不能為空',
        ];

        $v = Validator::make($credentials, [
            'mobile' => 'required',
        ], $messages);

        if ($v->fails()) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => '資料錯誤', 'result' => $v->errors()]);
        }
        $err = null;
        $error_code = $this->apiService->getErrorCode();

        $data = [];
        $token = $request->server->getHeaders()['AUTHORIZATION'];
        $response = $this->apiService->memberBsic($request['mobile'], $token);
        $result = json_decode($response, true);
        try {
            if ($result['status'] == '200') {
                $tmp = Members::where('member_id', '=', $result['data']['id'])->first();
                if (!is_null($tmp)) {
                    $token = Auth::guard('api')->fromUser($tmp);
                    Members::where('id', $tmp['id'])->update(['api_token' => $token]);
                } else {
                    $member = Members::create($result['data']['id']);
                    $token = Auth::guard('api')->fromUser($member);
                    Members::where('member_id', '=', $result['data']['id'])->update(['api_token' => $token]);
                }
                $result['data']['_token'] = $token;
                return response()->json(['status' => true, 'error_code' => null, 'message' => $result['message'], 'result' => $result['data']]);
            } else {
                $err = $result['status'];
                return response()->json(['status' => false, 'error_code' => $err, 'error_msg' => $result['message'], 'result' => (isset($result['error']) ? $result['error'] : [])]);
            }
        } catch (JWTException $e) {
            Log::info($e);
            $err = '404';
            return response()->json(['status' => false, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => []]);
        }
    }


}

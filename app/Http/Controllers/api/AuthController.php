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

        $fields = json_encode($credentials);
        $response = $this->apiService->memberLogin($fields);
        $result = json_decode($response, true);
        $error_code = $this->apiService->getErrorCode();
        unset($credentials['mobile']);
        unset($credentials['password']);
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

        /*
        //Request is validated
        //Crean token
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Login credentials are invalid.',
                ], 400);
            }
        } catch (JWTException $e) {
            return $credentials;
            return response()->json([
                'success' => false,
                'message' => 'Could not create token.',
            ], 500);
        }

        //Token created, return with success response and jwt token
        return response()->json([
            'success' => true,
            'token' => $token,
        ]);
        */

    }

    public function me()
    {
        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $token = JWTAuth::getToken();
        $member = Members::where('api_token','=', $token)->get();
        if ($member) {
            $message = 'token有效';
            $status = true;
        } else {
            $message = 'token無效';
            $status = false;
            $err = '203';
        }

        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => ['message'=>$message]]);
    }

    public function logout()
    {

        $token = JWTAuth::getToken();
        //dd($token);
        $member = Members::where('api_token','=',$token)->get();
        if (JWTAuth::parseToken()->invalidate()) {
            $message = 'token已失效';
            Members::where('id' , $member[0]['id'])->update(['api_token'=>'']);
            //Members::where('id', '=', $member[0]['id'])->delete();
        }
        return response()->json(['status' => true, 'error_code' => null, 'error_msg' => null, 'result' => ['message'=>$message]]);

    }

    public function profile()
    {
        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $token = JWTAuth::getToken();
        $member = Members::where('api_token','=', $token)->get();
        $response = $this->apiService->getMemberInfo($member[0]['member_id']);
        $result = json_decode($response, true);
        $data = [];
        $data['mobile'] = $result['data']['mobile'];
        $data['name'] = $result['data']['name'];
        $data['email'] = $result['data']['email'];
        $data['birthday'] = $result['data']['birthday'];
        $data['sex'] = $result['data']['sex'];
        $data['sexName'] = $result['data']['sexName'];
        $data['zipCode'] = $result['data']['zipCode'];
        $data['cityId'] = $result['data']['cityId'];
        $data['cityName'] = $result['data']['cityName'];
        $data['districtId'] = $result['data']['districtId'];
        $data['districtName'] = $result['data']['districtName'];
        $data['address'] = $result['data']['address'];
        try {
            if ($result['status'] == '200') {
                $status = true;
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
        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' =>  $data]);

    }

}

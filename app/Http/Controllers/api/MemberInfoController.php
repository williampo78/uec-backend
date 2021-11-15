<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Members;
use App\Services\APIService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;
use App\Services\APIWebService;
use Validator;
class MemberInfoController extends Controller
{

    public function __construct(APIService $apiService, APIWebService $apiWebService)
    {
        $this->apiService = $apiService;
        $this->apiWebService = $apiWebService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /*
     * 查詢會員資料 (會員編號)
     * @param  int  $id
     */
    public function profile()
    {
        $member_id = Auth::guard('api')->user()->member_id;

        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $response = $this->apiService->getMemberInfo($member_id);
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
        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $data]);

    }

    /*
     * 修改會員資料 (會員編號)
     */
    public function updateProfile(Request $request)
    {
        $data = [];
        $data['name'] = $request['name'];
        $data['email'] = $request['email'];
        $data['birthday'] = $request['birthday'];
        $data['sex'] = $request['sex'];
        $data['zipCode'] = $request['zipCode'];
        $data['cityId'] = $request['cityId'];
        $data['districtId'] = $request['districtId'];
        $data['address'] = $request['address'];

        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $response = $this->apiService->updateMemberInfo($data);
        $result = json_decode($response, true);

        try {
            if ($result['status'] == '200') {
                return response()->json(['status' => true, 'error_code' => null, 'error_msg' => null, 'result' => $result['message']]);
            } else {
                $err = $result['status'];
                if ($result['error']['email']) {
                    $message = $result['error']['email'];
                } else {
                    $message = $result['message'];
                }
                return response()->json(['status' => false, 'error_code' => $result['status'], 'error_msg' => $message, 'result' => []]);
            }
        } catch (JWTException $e) {
            Log::info($e);
            $err = '404';
            return response()->json(['status' => false, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => []]);
        }

    }

    /*
     * 會員 - 修改密碼 (會員編號)
     */
    public function changePassWord(Request $request)
    {
        $data = [];
        $data['oldPassword'] = $request['oldPassword'];
        $data['newPassword'] = $request['newPassword'];

        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $response = $this->apiService->changeMemberPassWord($data);
        $result = json_decode($response, true);
        try {
            if ($result['status'] == '200') {
                return response()->json(['status' => true, 'error_code' => null, 'error_msg' => null, 'result' => $result['message']]);
            } elseif ($result['status'] == '400') {
                /*
                $message = '';
                if (isset($result['error']['password'])) {
                    $message = $result['error']['password'];
                }
                if (isset($result['error']['oldPassword'])) {
                    $message .= $result['error']['oldPassword'];
                }
                if (isset($result['error']['newPassword'])) {
                    $message .= $result['error']['newPassword'];
                }
                */
                $message = '密碼格式錯誤';
                return response()->json(['status' => false, 'error_code' => $result['status'], 'error_msg' => $message, 'result' => []]);
            } elseif ($result['status'] == '401') {
                $message = '密碼錯誤';
                return response()->json(['status' => false, 'error_code' => $result['status'], 'error_msg' => $message, 'result' => []]);
            } elseif ($result['status'] == '404') {
                $message = '系統忙碌中，請稍後再試)';
                return response()->json(['status' => false, 'error_code' => $result['status'], 'error_msg' => $message, 'result' => []]);
            } else {
                $err = $result['status'];
                $message = $result['message'];
                return response()->json(['status' => false, 'error_code' => $result['status'], 'error_msg' => $message, 'result' => []]);
            }
            return response()->json(['status' => false, 'error_code' => $result['status'], 'error_msg' => $message, 'result' => []]);
        } catch (JWTException $e) {
            Log::info($e);
            $err = '404';
            return response()->json(['status' => false, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => []]);
        }

    }

    /*
     * 查詢會員收件人資料 (會員編號)
     * @param  int  $id
     */
    public function notes()
    {
        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $response = $this->apiWebService->getMemberNotes();
        $result = json_decode($response, true);
        if (count($result) > 0) {
            $status = true;
            $list = $result;
        } else {
            $status = false;
            $err = '404';
            $list = [];
        }
        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $list]);

    }

    /*
     * 編輯會員收件人資料 (會員編號)
     * @param  int  $id
     */
    public function updateNotes(Request $request, $id)
    {
        $err = null;
        $error_code = $this->apiService->getErrorCode();

        $messages = [
            'name.required' => '姓名不能為空',
            'mobile.required' => '手機不能為空',
            'email.required' => 'E-mail不能為空',
            'zip_code.required' => '郵遞區號不能為空',
            'city_name.required' => '縣市名稱不能為空',
            'city_id.required' => '縣市編號不能為空',
            'district_name.required' => '行政區不能為空',
            'district_id.required' => '行政區編號不能為空',
            'address.required' => '地址不能為空',
        ];

        $v = Validator::make($request->all(), [
            'name' => 'required',
            'mobile' => 'required',
            'email' => 'required',
            'zip_code' => 'required',
            'city_name' => 'required',
            'city_id' => 'required',
            'district_name' => 'required',
            'district_id' => 'required',
            'address' => 'required',
        ], $messages);

        if ($v->fails()){
            return response()->json(['status' => null, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $v->errors()]);
        }

        $response = $this->apiWebService->updateMemberNotes($request, $id);
        if ($response) {
            $status = true;
            $data = '更新成功';
        } else {
            $status = false;
            $err = '401';
            $data = '';
        }
        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $data]);

    }
}

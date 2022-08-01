<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\APIProductServices;
use App\Services\APIService;
use App\Services\APIWebService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class MemberInfoController extends Controller
{

    public function __construct(
        APIService $apiService,
        APIWebService $apiWebService,
        APIProductServices $apiProductServices
    )
    {
        $this->apiService = $apiService;
        $this->apiWebService = $apiWebService;
        $this->apiProductServices = $apiProductServices;
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
        try {
            if ($result['status'] == '200') {
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
                $data['recommendSource'] = $result['data']['recommendSource'];
                return response()->json(['status' => true, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $data]);
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
        $data['registeredSource'] = "EC";
        $data['recommendSource'] = $request['recommendSource'];
        if ($request['pwd'] != '') { //for 未手機驗證會員使用-可更新此欄位
            $data['password'] = $request['pwd'];
        }

        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $response = $this->apiService->updateMemberInfo($data);
        $result = json_decode($response, true);

        try {
            if ($result['status'] == '200') {
                if ($request['status'] == 'logout') {
                    Auth::guard('api')->logout();
                }
                return response()->json(['status' => true, 'error_code' => null, 'error_msg' => null, 'result' => $result['message']]);
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
            'zip_code' => 'required',
            'city_name' => 'required',
            'city_id' => 'required',
            'district_name' => 'required',
            'district_id' => 'required',
            'address' => 'required',
        ], $messages);

        if ($v->fails()) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => $error_code[401], 'result' => $v->errors()]);
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

    /*
     * 刪除會員收件人資料 (會員編號)
     * @param  int  $id
     */
    public function deleteNotes($id)
    {
        $err = null;
        $error_code = $this->apiService->getErrorCode();

        $response = $this->apiWebService->deleteMemberNotes($id);
        if ($response) {
            $status = true;
            $data = '刪除成功';
        } else {
            $status = false;
            $err = '401';
            $data = '';
        }
        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $data]);

    }

    /*
     * 新增會員收件人資料 (會員編號)
     * @param  int  $id
     */
    public function createNotes(Request $request)
    {
        $err = null;
        $error_code = $this->apiService->getErrorCode();

        $messages = [
            'string' => ':attribute 資料型態必須為string',
            'integer' => ':attribute 資料型態必須為integer',
            'in' => ':attribute 必須存在列表中的值: :values',
            'boolean' => ':attribute 資料型態必須為boolean',
            'note_type.required' => '類型不能為空',
            'name.required' => '姓名不能為空',
            'mobile.required' => '手機不能為空',
            'zip_code.required' => '郵遞區號不能為空',
            'city_name.required' => '縣市名稱不能為空',
            'city_id.required' => '縣市編號不能為空',
            'district_name.required' => '行政區不能為空',
            'district_id.required' => '行政區編號不能為空',
            'address.required' => '地址不能為空',
            'is_default.required' => '是否為預設收件地址不能為空',
        ];

        $v = Validator::make($request->all(), [
            'note_type' => 'required|string|in:HOME,FAMILY',
            'name' => 'required|string',
            'mobile' => 'required|string',
            'zip_code' => 'required|string',
            'city_name' => 'required|string',
            'city_id' => 'required|integer',
            'district_name' => 'required|string',
            'district_id' => 'required|integer',
            'address' => 'required|string',
            'cvs_type' => 'string|nullable',
            'cvs_store_no' => 'string|nullable',
            'is_default' => 'required|boolean',
        ], $messages);

        if ($v->fails()) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => $error_code[401], 'result' => $v->errors()]);
        }

        $response = $this->apiWebService->createMemberNote($request);

        if ($response == 200) {
            $status = true;
            $data = '新增成功';
        } elseif ($response == 405) {
            $status = false;
            $err = '405';
            $data = '新增失敗';
        } else {
            $status = false;
            $err = '401';
            $data = '新增失敗';
        }

        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $data]);
    }

    /*
     * 查詢會員商品收藏資料
     * @param
     */
    public function collections()
    {
        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $products = $this->apiProductServices->getProducts();
        //todo
        //$gtm = $this->apiProductServices->getProductItemForGTM($products);
        $response = $this->apiWebService->getMemberCollections();
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
     * 新增刪除會員商品收藏資料 (商品編號)
     * @param  int  $id
     */
    public function setCollections(Request $request)
    {
        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $messages = [
            'product_id.required' => '商品編號不能為空',
            'status.required' => '設定狀態不能為空',
        ];

        $v = Validator::make($request->all(), [
            'product_id' => 'required',
            'status' => 'required',
        ], $messages);

        if ($v->fails()) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => $error_code[401], 'result' => $v->errors()]);
        }

        $response = $this->apiWebService->setMemberCollections($request);
        if ($response == 'success') {
            $status = true;
            $data = ($request['status'] == 0 ? '加入' : '移除') . '收藏成功';
        } elseif ($response == '203') {
            $status = false;
            $err = $response;
            $data = '';
        } else {
            $status = false;
            $err = '401';
            $data = '';
        }
        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $data]);

    }

    /*
     * 批次刪除會員商品收藏資料 (商品編號)
     * @param  array(1,2,3)
     */
    public function batchDeleteCollections(Request $request)
    {
        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $messages = [
            'product_id.required' => '商品編號不能為空',
        ];

        $v = Validator::make($request->all(), [
            'product_id' => 'required',
        ], $messages);

        if ($v->fails()) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => $error_code[401], 'result' => $v->errors()]);
        }

        $response = $this->apiWebService->deleteMemberCollections($request);
        if ($response == 'success') {
            $status = true;
            $data = '移除收藏成功';
        } elseif ($response == '203') {
            $status = false;
            $err = $response;
            $data = '';
        } else {
            $status = false;
            $err = '401';
            $data = '';
        }
        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $data]);

    }

    /*
     * 查詢會員商品收藏資料 for 愛心顯示使用
     * @param
     */
    public function displayCollection()
    {
        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $response = $this->apiWebService->getMemberCollectiontoArray();
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

}

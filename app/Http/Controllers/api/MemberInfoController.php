<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Members;
use App\Services\APIService;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;

class MemberInfoController extends Controller
{

    public function __construct(APIService $apiService)
    {
        $this->apiService = $apiService;
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

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
}

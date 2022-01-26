<?php

namespace App\Http\Controllers\api;

use App\Services\APIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class MessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(APIService $apiService)
    {
        $this->apiService = $apiService;

    }
    /**
     * 取得訊息列表
     *
     */
    public function index(Request $request)
    {

        $member_id = Auth::guard('api')->user()->member_id;
        $url = '/drm/v1/members/' . $member_id . '/messages';
        $error_code = $this->apiService->getErrorCode();
        $err = null;
        $messages = [
            'page.required' => '頁數不能為空',
            'page.numeric' => '頁數必須為數值',
            'size.required' => '每頁筆數不能為空',
            'size.numeric' => '每頁筆數必須為數值',
        ];
        $input = Validator::make($request->all(), [
            'page' => 'required|numeric',
            'size' => 'required|numeric',
        ], $messages);
        if ($input->fails()) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => $error_code[401], 'result' => $input->errors()]);
        }
        $input = $input->safe()->only(['page', 'size']);
        $input['channels'] = 'EC,AIDRADVICE';
        $response = $this->apiService->getMessages($input, $url);
        $result = json_decode($response, true);

        try {
            if ($result['status'] == '200') {
                return response()->json(['status' => true, 'error_code' => $err, 'error_msg' => null, 'result' => $result['data']]);
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

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
    public function show(Request $request, $messageId)
    {
        $err = null;
        $member_id = Auth::guard('api')->user()->member_id;
        $url = '/drm/v1/members/' . $member_id . '/messages/' . $messageId;
        $error_code = $this->apiService->getErrorCode();
        $response = $this->apiService->showMessages($url);
        $result = json_decode($response, true);

        try {
            if ($result['status'] == '200') {
                return response()->json(['status' => true, 'error_code' => $err, 'error_msg' => null, 'result' => $result['data']]);
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * 更新已讀狀態
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $messageId)
    {
        $member_id = Auth::guard('api')->user()->member_id;
        $token = $request->server->getHeaders()['AUTHORIZATION'];
        $err = null;
        $error_code = $this->apiService->getErrorCode();
        $url = '/drm/v1/members/' . $member_id . '/messages/' . $messageId . '/change-read-status';
        $input = ["isRead" => true];
        $response = $this->apiService->changeReadStatusMessages($input, $url, $token);
        $result = json_decode($response, true);
        try {
            if ($result['status'] == '200') {
                return response()->json(['status' => true, 'error_code' => $err, 'error_msg' => null, 'result' => $result]);
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
    public function messageTop(Request $request)
    {

        $member_id = Auth::guard('api')->user()->member_id;
        $url = '/drm/v1/members/' . $member_id . '/top-messages';
        $error_code = $this->apiService->getErrorCode();
        $token = $request->server->getHeaders()['AUTHORIZATION'];
        $err = null;
        $messages = [
            'page.required' => '頁數不能為空',
            'page.numeric' => '頁數必須為數值',
            'size.required' => '每頁筆數不能為空',
            'size.numeric' => '每頁筆數必須為數值',
        ];
        $input = Validator::make($request->all(), [
            'page' => 'required|numeric',
            'size' => 'required|numeric',
        ], $messages);
        if ($input->fails()) {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => $error_code[401], 'result' => $input->errors()]);
        }
        $input = $input->safe()->only(['page', 'size']);
        $input['channels'] = 'EC,AIDRADVICE';
        $response = $this->apiService->getTopMessages($input, $url);
        $result = json_decode($response, true);

        try {
            if ($result['status'] == '200') {
                return response()->json(['status' => true, 'error_code' => $err, 'error_msg' => null, 'result' => $result['data']]);
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

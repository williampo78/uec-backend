<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;

class APIService
{

    /**
     * 取得錯誤代碼
     * @param
     * @return string
     */
    public function getErrorCode()
    {
        $code = [
            '' => null,
            '201' => '傳入無效的參數',
            '202' => '無效的Token',
            '203' => '無效的請求方式',
            '401' => '資料錯誤',
            '404' => '目前無資料',
            '405' => '資料已存在',
        ];
        return $code;
    }

    /*
     * 取得 crm api url
     * @return string
     */
    public function getURL()
    {
        if (config('uec.isTesting')) {
            return 'https://ssapi.dradvice.com.tw';
        } else {
            return 'https://ssapi.dradvice.com.tw';
        }

    }

    /*
     * 取得縣市鄉鎮
     * method: GET
     * @return json
     */
    public function getArea($all = null)
    {
        $curl = curl_init();
        if ($all) {
            $url = 'https://api.aidradvice.asia/area-all.json'; //(含離島，會員個資維護可能有離島)
        } else {
            $url = 'https://api.aidradvice.asia/area.json'; //(排除離島，商城配送不支援離島)
        }
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    /*
     * 會員登入
     * method: POST
     * @return json
     */
    public function memberLogin($input)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getURL() . '/crm/v1/members/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $input,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    /*
     * 查詢會員資料
     * method: GET
     * @return json
     */
    public function getMemberInfo($input)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getURL() . '/crm/v1/members/' . $input,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    /*
     * 修改會員資料
     * method: PATCH
     * @return json
     */
    public function updateMemberInfo($input)
    {
        $curl = curl_init();
        $member_id = Auth::guard('api')->user()->member_id;
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getURL() . '/crm/v1/members/' . $member_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PATCH',
            CURLOPT_POSTFIELDS => json_encode($input),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    /*
     * 修改會員密碼
     * method: POST
     * @return json
     */
    public function changeMemberPassWord($input)
    {
        $curl = curl_init();
        $member_id = Auth::guard('api')->user()->member_id;
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getURL() . '/crm/v1/members/' . $member_id . '/change-password',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($input),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    /*
     * 查詢會員可用點數歷程
     * method: GET
     * @return json
     */
    public function getPointInfo($input)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getURL() . '/crm/v1/members/' . $input . '/point-logs',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    /*
     * 查詢會員即將到期點數歷程
     * method: GET
     * @return json
     */
    public function getExpiringPointInfo($input)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getURL() . '/crm/v1/members/' . $input . '/expiring-point-logs',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }

    /*
     * 會員註冊
     * method: POST
     * @return json
     */
    public function memberRegistration($input, $token)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getURL() . '/crm/v1/members',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($input),
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $token,
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;

    }

    /*
     * 會員註冊 (查詢會員簡訊驗證狀態)
     * method: GET
     * @return json
     */
    public function getMemberSMSStatus($input)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getURL() . '/crm/v1/members/get-status?mobile=' . $input,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;

    }

    /*
     * 會員註冊 (發送驗證簡訊)
     * method: POST
     * @return json
     */
    public function sendMemberSMS($input)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getURL() . '/crm/v1/auth/sms/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($input),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;

    }

    /*
     * 會員註冊 (驗證簡訊)
     * method: POST
     * @return json
     */
    public function verifyMemberSMS($input)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getURL() . '/crm/v1/auth/sms/verify',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($input),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;

    }

    /*
     * 查詢會員基本
     * method: GET
     * @return json
     */
    public function memberBsic($input, $token)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getURL() . '/crm/v1/members/basic?mobile=' . $input,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $token,
                'Content-Type: application/json',
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;

    }

    /*
     * 查詢會員可用點數
     * method: GET
     * @return json
     */
    public function getMemberPoint($input)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getURL() . '/crm/v1/members/' . $input . '/point',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
    /*
     * 訊息中心
     */
    public function getMessages($input, $url)
    {
        $client = new Client();
        $result = $client->request('GET', $this->getURL() . $url, [
            'query' => $input,
            'http_errors' => false,
            'headers' => [
                // 'Authorization'     => $token ,
                'Content-Type: application/json',
            ],
        ])->getBody()->getContents();
        return $result;
    }
    /**
     * 更改訊息讀取狀態
     *
     */
    public function changeReadStatusMessages($input, $url, $token)
    {
        $client = new Client();
        $response = $client->request('PATCH', $this->getURL() . $url, [
            'json' => $input,
            'http_errors' => false,
            'headers' => [
                'Content-Type: application/json',
            ],
        ])->getBody()->getContents();

        return $response;
    }
    /**
     * 秀出訊息
     */
    public function showMessages($url)
    {
        $client = new Client();
        $response = $client->request('get', $this->getURL() . $url, [
            // 'json' => $input,
            'http_errors' => false,
            'headers' => [
                'Content-Type: application/json',
            ],
        ])->getBody()->getContents();
        return $response;
    }
}

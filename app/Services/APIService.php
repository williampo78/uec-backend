<?php


namespace App\Services;


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
            '401' => '密碼錯誤',
            '404' => '資料不存在'
        ];
        return $code;
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
            CURLOPT_URL => 'https://stgapi.dradvice.com.tw/crm/v1/members/login',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>$input,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}

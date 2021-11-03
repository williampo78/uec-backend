<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

use App\Services\APIService;
class DradviceController extends Controller
{

    private $apiService;

    public function __construct(APIService $apiService)
    {
        $this->apiService = $apiService;
    }

    /**
     * 縣市鄉鎮下拉選單
     */
    public function area()
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://18.178.42.56:8020/area.json',
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

        $status = false;
        $err = null;
        $error_code = $this->apiService->getErrorCode();
        if ($response) {
            $status= true;
            $response = json_decode($response, true);
        } else {
            $response = [];
            $status = false;
            $err = '201';
        }
        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $response]);
    }
}

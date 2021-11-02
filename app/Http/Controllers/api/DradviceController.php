<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

class DradviceController extends Controller
{

    /**
     * 縣市鄉鎮下拉選單
     */
    public function area()
    {
        $area_api = 'http://18.178.42.56:8020/area.json';
        //  Initiate curl
        $ch = curl_init();
        // Will return the response, if false it print the response
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Set the url
        curl_setopt($ch, CURLOPT_URL, $area_api);
        // Execute
        $result = curl_exec($ch);
        // Closing
        curl_close($ch);
        return $result;
    }
}

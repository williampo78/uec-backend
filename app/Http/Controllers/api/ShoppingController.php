<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\APICartServices;
use App\Services\APIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class ShoppingController extends Controller
{

    private $apiCartService;

    public function __construct(APICartServices $apiCartService, APIService $apiService)
    {
        $this->apiCartService = $apiCartService;
        $this->apiService = $apiService;
    }

    public function getCartCount()
    {
        $error_code = $this->apiService->getErrorCode();
        $login = Auth::guard('api')->check();
        if ($login) {
            $member_id = Auth::guard('api')->user()->member_id;
            if ($member_id > 0) {
                $cartCount = $this->apiCartService->getCartCount($member_id);
            }
            $result = '200';
        } else {
            $result = '404';
        }
        if ($result == '404') {
            $status = false;
            $err = '404';
            $list = array("member" => false, "count" => 0);
        } else {
            $status = true;
            $err = null;
            $list = array("member" => true, "count" => $cartCount);
        }
        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $list]);
    }
}

<?php


namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\ShoppingCart;
class APICartServices
{

    public function __construct()
    {
    }

    /*
     * 取得購物車內容
     *
     */
    public function getCartInfo($id)
    {
        $result = ShoppingCart::where('member_id', '=', $id)
            ->where('status_code','=',0)->get();
        return $result;
    }
}

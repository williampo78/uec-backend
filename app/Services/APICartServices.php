<?php


namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\ShoppingCartDetails;

class APICartServices
{

    public function __construct()
    {
    }


    /*
     * 取得購物車內容
     *
     */
    public function getCartInfo($member_id)
    {
        $result = ShoppingCartDetails::where('member_id', '=', $member_id)
            ->where('status_code', '=', 0)->get();
        return $result;
    }

    /*
     * 取得會員目前購物車數量
     * @params:member_id
     */
    public function getCartCount($member_id)
    {
        $wordCount = ShoppingCartDetails::where('member_id', '=', $member_id)
            ->where('status_code', '=', 0)->count();
        return $wordCount;
    }
}

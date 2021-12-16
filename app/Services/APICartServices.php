<?php


namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\ShoppingCartDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ProductItems;

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

    /**
     * 會員購物車(新增/刪除/編輯)
     * @param
     * @return string
     */
    public function setMemberCart($input)
    {
        $member_id = Auth::guard('api')->user()->member_id;
        $now = Carbon::now();
        //確認是否有該品項
        $item = ProductItems::where('id', $input['item_id'])->where('item_no', $input['item_no'])->get()->toArray();
        if (count($item) > 0) {
            $data = ShoppingCartDetails::where('product_item_id', $input['item_id'])->where('member_id', $member_id)->get()->toArray();
            if (count($data) > 0) {
                $act = 'upd';
            } else {
                $act = 'add';
            }
        } else {
            return '401';
        }
        DB::beginTransaction();
        try {
            $webData = [];
            if ($act == 'add') {
                $webData['member_id'] = $member_id;
                $webData['product_item_id'] = $input['item_id'];
                $webData['status_code'] = $input['status_code'];
                $webData['qty'] = $input['item_qty'];
                $webData['utm_source'] = $input['utm_source'];
                $webData['utm_medium'] = $input['utm_medium'];
                $webData['utm_campaign'] = $input['utm_campaign'];
                $webData['utm_sales'] = $input['utm_sales'];
                $webData['utm_time'] = $input['utm_time'];
                $webData['created_by'] = $member_id;
                $webData['updated_by'] = -1;
                $webData['created_at'] = $now;
                $webData['updated_at'] = $now;
                if ($input['status_code'] != 0) {
                    return '203';
                }
                $new_id = ShoppingCartDetails::insertGetId($webData);
            } else if ($act == 'upd') {
                $webData['qty'] = $input['item_qty'];
                $webData['status_code'] = $input['status_code'];
                $webData['updated_by'] = $member_id;
                $webData['updated_at'] = $now;
                $new_id = ShoppingCartDetails::where('product_item_id', $input['item_id'])->where('member_id', $member_id)->update($webData);
            }
            DB::commit();
            if ($new_id > 0) {
                $result = 'success';
            } else {
                $result = 'fail';
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::info($e);
            $result = 'fail';
        }

        return $result;
    }
}

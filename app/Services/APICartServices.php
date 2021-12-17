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

    /*
     * 取得購物車內容
     *
     */
    public function getCartInfo($member_id)
    {
        $result = ShoppingCartDetails::select("products.id as product_id", "products.product_name", "products.list_price", "products.selling_price", "products.start_launched_at", "products.end_launched_at"
            , "product_items.id as item_id", "shopping_cart_details.qty as item_qty", "product_items.spec_1_value as item_spec1", "product_items.spec_2_value as item_spec2"
            , "product_items.item_no", "product_items.photo_name as item_photo", "product_items.status as item_status")
            ->where('shopping_cart_details.member_id', $member_id)
            ->where('shopping_cart_details.status_code', 0)//購物車
            ->join('product_items', 'product_items.id', '=', 'shopping_cart_details.product_item_id')
            ->join('products', 'products.id', '=', 'product_items.product_id')
            ->where('products.approval_status', '=', 'APPROVED')//核準上架
            ->orderBy('product_items.sort', 'asc')
            ->get();

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

    /*
     * 取得購物車內容
     *
     */
    public function getCartData($member_id, $campaigns, $campaign_gift)
    {
        $now = Carbon::now();
        //購物車內容
        $cartInfo = self::getCartInfo($member_id);
        $cartQty = [];
        $cartAmount = [];
        $cartDetail = [];
        foreach ($cartInfo as $items => $item) {
            $cartQty[$item->product_id][$item->item_id] = $item->item_qty; //購物車數量
            $cartAmount[$item->product_id] = intval($item->selling_price); //商品售價
            $cartDetail[$item->product_id][$item->item_id] = $item; //購物車數量
        }

        //行銷活動
        foreach ($campaigns as $product_id => $item) {
            foreach ($item as $k => $v) {
                if ($now >= $v->start_at and $now <= $v->end_at) { //活動時間內才做
                    //先取活動類型為單品或車子(贈品或折扣) $campaign['PRD']['DISCOUNT'] 單品折扣
                    $campaign[$v->level_code][$v->category_code][$product_id] = $v;
                }
            }
        }
        //取滿額折扣
        $product = [];
        foreach ($cartQty as $product_id => $item) {
            if ($now >= $cartInfo[$product_id]['start_launched_at'] && $now <= $cartInfo[$product_id]['end_launched_at']) { //在上架期間內
                $qty = array_sum($item); //合併不同規格但同一商品的數量
                if (isset($campaign['PRD']['DISCOUNT'][$product_id])) { //在活動內
                    //ex: n=2, x=0.85, qty=4, price = 1000
                    $unit_qty = ($qty - $campaign['PRD']['DISCOUNT'][$product_id]->n_value + 1); //有幾件可以打折(4-2+1)
                    if ($qty >= $campaign['PRD']['DISCOUNT'][$product_id]->n_value && $unit_qty > 0) {
                        if ($campaign['PRD']['DISCOUNT'][$product_id]->campaign_type == 'PRD01') { //﹝單品﹞第N件(含)以上，打X折
                            $price = $cartAmount[$product_id] * $campaign['PRD']['DISCOUNT'][$product_id]->x_value; //打折後1件單價 1000*0.85
                            foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                                $tmp_qty = $detail_qty;
                                if ($unit_qty >= $tmp_qty) { // 3 >= 2
                                    //item_id折多少
                                    $amount = $tmp_qty * $price;
                                    $return_qty = $tmp_qty;
                                    $unit_price = round($amount / $return_qty);
                                    $unit_qty = $unit_qty - $tmp_qty; //3-2=1
                                    $return_type = true;
                                } else {
                                    if ($unit_qty > 0) {
                                        if ($unit_qty < $detail_qty) {
                                            $tmp1 = $unit_qty; //要打折的數量
                                        } else {
                                            $tmp1 = ($detail_qty - $unit_qty); //要打折的數量
                                        }
                                        $tmp2 = ($detail_qty - $tmp1); //不打折的數量
                                        $price1 = $tmp1 * $price; //1*850
                                        $price2 = $tmp2 * $cartAmount[$product_id]; //1*1000
                                        $amount = ($price1 + $price2);
                                        $return_qty = $tmp_qty;
                                        $unit_price = round($amount / ($tmp1 + $tmp2));
                                        $unit_qty = $unit_qty - $tmp_qty; //3-2=1
                                        $return_type = true;
                                    } else {
                                        $amount = $tmp_qty * $cartAmount[$product_id];
                                        $return_qty = $tmp_qty;
                                        $unit_price = round($amount / $return_qty);
                                        $unit_qty = $unit_qty - $tmp_qty; //3-2=1
                                        $return_type = true;
                                    }
                                }
                                $product[$product_id][$item_id] = array(
                                    "product_name" => $cartDetail[$product_id][$item_id]->product_name,
                                    "product_photo" => $cartDetail[$product_id][$item_id]->item_photo,
                                    "item_id" => $cartDetail[$product_id][$item_id]->item_id,
                                    "item_no" => $cartDetail[$product_id][$item_id]->item_no,
                                    "item_spec1" => $cartDetail[$product_id][$item_id]->item_spec1,
                                    "item_spec2" => $cartDetail[$product_id][$item_id]->item_spec2,
                                    "item_price" => $unit_price,
                                    "item_qty" => $return_qty,
                                    "amount" => $amount,
                                    "campaign_name" => $campaign['PRD']['DISCOUNT'][$product_id]->campaign_name,
                                    "campaign_type" => $return_type,
                                    "product_type" => "effective"
                                );
                            }
                        } elseif ($campaign['PRD']['DISCOUNT'][$product_id]->campaign_type == 'PRD02') { //﹝單品﹞第N件(含)以上，折X元
                            $price = $cartAmount[$product_id] - $campaign['PRD']['DISCOUNT'][$product_id]->x_value; //打折後1件單價 1000-200
                            foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                                $tmp_qty = $detail_qty;
                                if ($unit_qty >= $tmp_qty) { // 3 >= 2
                                    //item_id折多少
                                    $amount = $tmp_qty * $price;
                                    $return_qty = $tmp_qty;
                                    $unit_price = round($amount / $return_qty);
                                    $unit_qty = $unit_qty - $tmp_qty; //3-2=1
                                    $return_type = true;
                                } else {
                                    if ($unit_qty > 0) {
                                        if ($unit_qty < $detail_qty) {
                                            $tmp1 = $unit_qty; //要打折的數量
                                        } else {
                                            $tmp1 = ($detail_qty - $unit_qty); //要打折的數量
                                        }
                                        $tmp2 = ($detail_qty - $tmp1); //不打折的數量
                                        $price1 = $tmp1 * $price; //1*850
                                        $price2 = $tmp2 * $cartAmount[$product_id]; //1*1000
                                        $return_qty = $tmp_qty;
                                        $amount = ($price1 + $price2);
                                        $unit_price = round($amount / ($tmp1 + $tmp2));
                                        $unit_qty = $unit_qty - $tmp_qty; //3-2=1
                                        $return_type = true;
                                    } else {
                                        $amount = $tmp_qty * $cartAmount[$product_id];
                                        $return_qty = $tmp_qty;
                                        $unit_price = round($amount / $return_qty);
                                        $unit_qty = $unit_qty - $tmp_qty; //3-2=1
                                        $return_type = true;
                                    }
                                }
                                $product[$product_id][$item_id] = array(
                                    "product_name" => $cartDetail[$product_id][$item_id]->product_name,
                                    "product_photo" => $cartDetail[$product_id][$item_id]->item_photo,
                                    "item_id" => $cartDetail[$product_id][$item_id]->item_id,
                                    "item_no" => $cartDetail[$product_id][$item_id]->item_no,
                                    "item_spec1" => $cartDetail[$product_id][$item_id]->item_spec1,
                                    "item_spec2" => $cartDetail[$product_id][$item_id]->item_spec2,
                                    "item_price" => $unit_price,
                                    "item_qty" => $return_qty,
                                    "amount" => $amount,
                                    "campaign_name" => $campaign['PRD']['DISCOUNT'][$product_id]->campaign_name,
                                    "campaign_type" => $return_type,
                                    "product_type" => "effective"
                                );
                            }
                        } elseif ($campaign['PRD']['DISCOUNT'][$product_id]->campaign_type == 'PRD03') { //﹝單品﹞滿N件，每件打X折
                            $price = $cartAmount[$product_id] * $campaign['PRD']['DISCOUNT'][$product_id]->x_value; //打折後每件單價 1000*0.85
                            foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                                $tmp_qty = $detail_qty;
                                $amount = $tmp_qty * $price;
                                $return_qty = $tmp_qty;
                                $unit_price = round($amount / $return_qty);
                                $product[$product_id][$item_id] = array(
                                    "product_name" => $cartDetail[$product_id][$item_id]->product_name,
                                    "product_photo" => $cartDetail[$product_id][$item_id]->item_photo,
                                    "item_id" => $cartDetail[$product_id][$item_id]->item_id,
                                    "item_no" => $cartDetail[$product_id][$item_id]->item_no,
                                    "item_spec1" => $cartDetail[$product_id][$item_id]->item_spec1,
                                    "item_spec2" => $cartDetail[$product_id][$item_id]->item_spec2,
                                    "item_price" => $unit_price,
                                    "item_qty" => $return_qty,
                                    "amount" => $amount,
                                    "campaign_name" => $campaign['PRD']['DISCOUNT'][$product_id]->campaign_name,
                                    "campaign_type" => true,
                                    "product_type" => "effective"
                                );
                            }
                        } elseif ($campaign['PRD']['DISCOUNT'][$product_id]->campaign_type == 'PRD04') { //﹝單品﹞滿N件，每件折X元
                            $price = $cartAmount[$product_id] - $campaign['PRD']['DISCOUNT'][$product_id]->x_value; //打折後每件單價 1000-200
                            foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                                $tmp_qty = $detail_qty;
                                $amount = $tmp_qty * $price;
                                $return_qty = $tmp_qty;
                                $unit_price = round($amount / $return_qty);
                                $product[$product_id][$item_id] = array(
                                    "product_name" => $cartDetail[$product_id][$item_id]->product_name,
                                    "product_photo" => $cartDetail[$product_id][$item_id]->item_photo,
                                    "item_id" => $cartDetail[$product_id][$item_id]->item_id,
                                    "item_no" => $cartDetail[$product_id][$item_id]->item_no,
                                    "item_spec1" => $cartDetail[$product_id][$item_id]->item_spec1,
                                    "item_spec2" => $cartDetail[$product_id][$item_id]->item_spec2,
                                    "item_price" => $unit_price,
                                    "item_qty" => $return_qty,
                                    "amount" => $amount,
                                    "campaign_name" => $campaign['PRD']['DISCOUNT'][$product_id]->campaign_name,
                                    "campaign_type" => true,
                                    "product_type" => "effective"
                                );
                            }
                        }
                    } else { //沒有打折的件數
                        foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                            $product[$product_id][$item_id] = array(
                                "product_name" => $cartDetail[$product_id][$item_id]->product_name,
                                "product_photo" => $cartDetail[$product_id][$item_id]->item_photo,
                                "item_id" => $cartDetail[$product_id][$item_id]->item_id,
                                "item_no" => $cartDetail[$product_id][$item_id]->item_no,
                                "item_spec1" => $cartDetail[$product_id][$item_id]->item_spec1,
                                "item_spec2" => $cartDetail[$product_id][$item_id]->item_spec2,
                                "item_price" => intval($cartDetail[$product_id][$item_id]->selling_price),
                                "item_qty" => $detail_qty,
                                "amount" => intval($cartDetail[$product_id][$item_id]->selling_price * $detail_qty),
                                "campaign_name" => null,
                                "campaign_type" => false,
                                "product_type" => "effective"
                            );
                        };
                    }
                } else { //不在活動內
                    foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                        $product[$product_id][$item_id] = array(
                            "product_name" => $cartDetail[$product_id][$item_id]->product_name,
                            "product_photo" => $cartDetail[$product_id][$item_id]->item_photo,
                            "item_id" => $cartDetail[$product_id][$item_id]->item_id,
                            "item_no" => $cartDetail[$product_id][$item_id]->item_no,
                            "item_spec1" => $cartDetail[$product_id][$item_id]->item_spec1,
                            "item_spec2" => $cartDetail[$product_id][$item_id]->item_spec2,
                            "item_price" => intval($cartDetail[$product_id][$item_id]->selling_price),
                            "item_qty" => $detail_qty,
                            "amount" => intval($cartDetail[$product_id][$item_id]->selling_price * $detail_qty),
                            "campaign_name" => null,
                            "campaign_type" => false,
                            "product_type" => "effective"
                        );
                    };
                }
                if (isset($campaign['PRD']['GIFT'][$product_id])) {
                    echo $campaign['PRD']['GIFT'][$product_id]->x_value;
                }
            } else {
                foreach ($item as $item_id => $detail_qty) { //取得item規格數量
                    $product[$product_id][$item_id] = array(
                        "product_name" => $cartDetail[$product_id][$item_id]->product_name,
                        "product_photo" => $cartDetail[$product_id][$item_id]->item_photo,
                        "item_id" => $cartDetail[$product_id][$item_id]->item_id,
                        "item_no" => $cartDetail[$product_id][$item_id]->item_no,
                        "item_spec1" => $cartDetail[$product_id][$item_id]->item_spec1,
                        "item_spec2" => $cartDetail[$product_id][$item_id]->item_spec2,
                        "item_price" => intval($cartDetail[$product_id][$item_id]->selling_price),
                        "item_qty" => $detail_qty,
                        "amount" => intval($cartDetail[$product_id][$item_id]->selling_price * $detail_qty),
                        "campaign_name" => null,
                        "campaign_type" => false,
                        "product_type" => "expired"
                    );
                }
            }
        }

        //取滿額贈品
        dd();
        //運費規則

        return $campaign;
    }
}

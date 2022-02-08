<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\ShoppingCartDetails;
use App\Services\APIService;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class StockController extends Controller
{
    public function __construct(APIService $apiService, StockService $stockService)
    {
        $this->apiService = $apiService;
        $this->stockService = $stockService;
    }

    //
    public function getItemStock(Request $request)
    {
        $error_code = $this->apiService->getErrorCode();
        if ($request->item_id == '') {
            return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => $error_code[401], 'result' => '產品編號不能為空']);
        } else {
            $messages = [
                'item_id.numeric' => '產品編號必須是數字',
            ];

            $v = Validator::make($request->all(), [
                'item_id' => 'numeric',
            ], $messages);

            if ($v->fails()) {
                return response()->json(['status' => false, 'error_code' => '401', 'error_msg' => $error_code[401], 'result' => $v->errors()]);
            }
        }
        $warehouseCode = $this->stockService->getWarehouseConfig();
        $result = $this->stockService->getStockByItem($warehouseCode, $request['item_id']);

        $login = Auth::guard('api')->check();
        $shopCartQty = 0;
        if ($login) {
            $member_id = Auth::guard('api')->user()->member_id;
            if ($member_id > 0) {
                $shoppingCart = ShoppingCartDetails::where('status_code', 0)->where('product_item_id', $request['item_id'])->where('member_id', $member_id)->get()->toArray();
                if (count($shoppingCart) > 0) {
                    $shopCartQty = $shoppingCart[0]['qty'];
                }
            }
        } else {
            $shopCartQty = 0;
        }
        if ($result) {
            $status = true;
            $err = '';
            $data = $result;
            unset($result->warehouse_id);
            unset($result->id);
            $data['specifiedQty'] = ($result->stockQty <= $result->limitedQty ? $result->stockQty : $result->limitedQty) - $shopCartQty;
        } else {
            $status = false;
            $err = '404';
            $data = 0;
        }
        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $data]);

    }
}

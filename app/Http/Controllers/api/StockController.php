<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\APIService;
use App\Services\StockService;
use Illuminate\Http\Request;
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
        $result = $this->stockService->getStockByItem('WHS01', $request['item_id']);
        if ($result) {
            $status = true;
            $err = '';
            $data = $result;
            $data['specifiedQty'] = ($result->stockQty <= $result->limitedQty ? $result->stockQty : $result->limitedQty);
        } else {
            $status = false;
            $err = '404';
            $data = 0;
        }
        return response()->json(['status' => $status, 'error_code' => $err, 'error_msg' => $error_code[$err], 'result' => $data]);

    }
}

<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\ProductsService;
use Illuminate\Support\Facades\Log;

class PromotionalCampaignController extends Controller
{
    /**
     * 取得商品資料
     *
     * @param Request $request
     * @return json
     */
    public function getProducts(Request $request)
    {
        $product_service = new ProductsService;
        $input_data = $request->input();
        $products = $product_service->getProducts($input_data);

        if (!empty($input_data['exist_products'])) {
            // 過濾已存在的商品
            $products = $products->filter(function ($obj, $key) use ($input_data) {
                return !in_array($obj->id, $input_data['exist_products']);
            });
        }

        $products = $products->mapWithKeys(function ($obj, $key) {
            // 上架日期
            $obj->launched_at = ($obj->start_launched_at || $obj->end_launched_at) ? "{$obj->start_launched_at} ~ {$obj->end_launched_at}" : '';

            // 售價
            $obj->selling_price = number_format($obj->selling_price);

            // 上架狀態
            switch ($obj->approval_status) {
                case 'NA':
                    $obj->launched_status = '未設定';
                    break;

                case 'REVIEWING':
                    $obj->launched_status = '上架申請';
                    break;

                case 'REJECTED':
                    $obj->launched_status = '上架駁回';
                    break;

                case 'CANCELLED':
                    $obj->launched_status = '商品下架';
                    break;

                case 'APPROVED':
                    $obj->launched_status = Carbon::now()->between($obj->start_launched_at, $obj->end_launched_at) ? '商品上架' : '商品下架';
                    break;
            }

            // 毛利
            $obj->gross_margin = 10;

            return [
                $obj->id => $obj->only([
                    'launched_at',
                    'id',
                    'product_name',
                    'product_no',
                    'selling_price',
                    'supplier_name',
                    'launched_status',
                    'gross_margin',
                ])
            ];
        });

        return response()->json($products);
    }
}

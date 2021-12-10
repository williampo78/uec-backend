<?php

namespace App\Http\Controllers;

use App\Services\ProductsService;
use Illuminate\Http\Request;

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
        $products_service = new ProductsService;
        $input_data = $request->input();
        $products = $products_service->getProducts($input_data);

        if (!empty($input_data['exist_products'])) {
            // 過濾已存在的商品
            $products = $products->filter(function ($obj, $key) use ($input_data) {
                return !in_array($obj->id, $input_data['exist_products']);
            });
        }

        $products_service->restructureProducts($products);

        $products = $products->mapWithKeys(function ($product) {
            return [
                $product->id => $product->only([
                    'launched_at',
                    'product_name',
                    'product_no',
                    'selling_price',
                    'supplier_name',
                    'launched_status',
                    'gross_margin',
                ]),
            ];
        });

        return response()->json($products);
    }
}

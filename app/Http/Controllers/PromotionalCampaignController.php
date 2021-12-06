<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProductsService;
use Illuminate\Support\Facades\Log;

class PromotionalCampaignController extends Controller
{
    public function getProducts(Request $request)
    {
        $product_service = new ProductsService;
        $input_data = $request->input();
        $products = $product_service->getProducts($input_data);

        // Log::info($input_data);
        // Log::info($products);

        return response()->json([]);
    }
}

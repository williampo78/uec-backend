<?php

namespace App\Services;

use App\Models\Products;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Services\UniversalService;
class ProductsService
{
    private $universalService;

    public function __construct(UniversalService $universalService)
    {
        $this->universalService = $universalService ; 
    }

    public function getProducts($data = [])
    {
        $agent_id = Auth::user()->agent_id;

        $result = Products::where('agent_id', $agent_id)
            ->get();

        return $result;
    }
    public function addProducts($in, $file)
    {
        $user_id = Auth::user()->id;
        $agent_id = Auth::user()->agent_id;
        $now = Carbon::now();
        $skuList = json_decode($in['SkuListdata']);
        $insert = [
            'stock_type' => $in['stock_type'],
            'product_no' => $this->universalService->getDocNumber('products', $in['stock_type']) ,
            'supplier_id' => $in['supplier_id'],
            'product_name' => $in['product_name'],
            'tax_type' => $in['tax_type'],
            'category_id' => $in['category_id'],
            'brand_id' => $in['brand_id'],
            'model' => $in['model'],
            'lgst_method' => $in['lgst_method'],
            'lgst_temperature' => $in['lgst_temperature'],
            'uom' => $in['uom'],
            'min_purchase_qty' => $in['min_purchase_qty'],
            'has_expiry_date' => $in['has_expiry_date'],
            'expiry_days' => $in['expiry_days'],
            'expiry_receiving_days' => $in['expiry_receiving_days'],
            'product_type' => $in['product_type'],
            'is_discontinued' => $in['is_discontinued'],
            'length' => $in['length'],
            'width' => $in['width'],
            'height' => $in['height'],
            'weight' => $in['weight'],
            'list_price' => $in['list_price'],
            'selling_price' => $in['selling_price'],
            'product_brief_1' => $in['product_brief_1'],
            'product_brief_2' => $in['product_brief_2'],
            'product_brief_3' => $in['product_brief_3'],
            'patent_no' => $in['patent_no'],
            'is_with_warranty' => $in['is_with_warranty'],
            'warranty_days' => $in['warranty_days'],
            'warranty_scope' => $in['warranty_scope'],
            'spec_dimension' => $in['spec_dimension'],
            'created_by' => $user_id, 
            'updated_by' => $user_id, 
            'created_at' => $now, 
            'updated_at' => $now,
            'agent_id' => $agent_id, 
        ];
        
        $products_id = Products::insertGetid($insert);
        dd($products_id) ; 
        // $skuList['insert'] = '' ;

        dd($skuList);
        exit;
    }

}

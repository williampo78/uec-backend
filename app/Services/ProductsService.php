<?php

namespace App\Services;

use App\Models\ProductItems;
use App\Models\Products;
use App\Models\ProductPhotos;
use App\Services\UniversalService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use ImageUpload ;

class ProductsService
{
    private $universalService;

    public function __construct(UniversalService $universalService)
    {
        $this->universalService = $universalService;
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
        $skuList = json_decode($in['SkuListdata'], true);
        DB::beginTransaction();
        try {
            $insert = [
                'stock_type' => $in['stock_type'],
                'product_no' => $this->universalService->getDocNumber('products', $in['stock_type']),
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
                'selling_channel' => $in['selling_channel'],
                'delivery_type' => $in['delivery_type'],
                'created_by' => $user_id,
                'updated_by' => $user_id,
                'created_at' => $now,
                'updated_at' => $now,
                'agent_id' => $agent_id,
            ];
            $products_id = Products::create($insert)->id;
            $products = Products::findOrFail($products_id);
            foreach ($skuList as $key => $val) {
                $skuInsert = [
                    'agent_id' => $agent_id,
                    'product_id' => $products->id,
                    'sort' => $val['sort'],
                    'spec_1_value' => $val['spec_1_value'],
                    'spec_2_value' => $val['spec_2_value'],
                    'item_no' => $products->product_no . str_pad($key, 4, "0", STR_PAD_LEFT), //新增時直接用key生成id
                    'supplier_item_no' => '',
                    'ean' => $val['ean'],
                    'pos_item_no' => $val['pos_item_no'],
                    'safty_qty' => $val['safty_qty'],
                    'is_additional_purchase' => $val['is_additional_purchase'],
                    'status' => $val['status'],
                    'created_by' => $user_id,
                    'updated_by' => $user_id,
                    'created_at' => $now,
                    'updated_at' => $now,

                ];
                ProductItems::create($skuInsert);
            }
            $fileList = [] ;
            $uploadPath = '/products/' . $products->id ; 
            foreach($file['filedata'] as $key => $val){
                $fileList[$key] = ImageUpload::uploadImage($val,$uploadPath) ; 
            }
            foreach($fileList as $key => $val) {
                $insertImg = [
                    'product_id' =>  $products->id,
                    'photo_name' =>  $val['image'],
                    'sort' => $key ,
                    'created_by' => $user_id,
                    'updated_by' => $user_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            DB::commit();
            $result = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::warning($e->getMessage());
            $result = false;
        }
        
        
        return $result;
    }

}

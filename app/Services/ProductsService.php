<?php

namespace App\Services;

use App\Models\ProductItems;
use App\Models\Products;
use App\Models\ProductPhotos;
use App\Services\UniversalService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use ImageUpload;

class ProductsService
{
    private $universalService;

    public function __construct(UniversalService $universalService)
    {
        $this->universalService = $universalService;
    }

    public function getProducts($in = [])
    {
        $agent_id = Auth::user()->agent_id;
        
        $Products = Products::where('agent_id', $agent_id);
        //庫存類型
        if (isset($in['stock_type'])) {
            $Products->where('stock_type', '=', $in['stock_type']);
        }
        //商品編號
        if (isset($in['product_no'])) {
            $product_no = explode(',',$in['product_no']);
            foreach($product_no as $key => $val){
                if($key == 0){
                    $Products->where('product_no', 'like', '%' . $val . '%');
                }else{
                    $Products->orWhere('product_no', 'like', '%' . $val . '%');
                }
            }
        }
        //供應商
        if (isset($in['supplier_id'])) {
            $Products->where('supplier_id', $in['supplier_id']);
        }
        //商品通路
        if(isset($in['selling_channel'])){
            $Products->where('selling_channel', $in['selling_channel']);
        }
        //商品名稱
        if (isset($in['product_name'])) {
            $Products->where('product_name', 'like', '%' . $in['product_name'] . '%');
        }
        //前台分類
        if(isset($in['category_id'])){
            $Products->where('category_id' , $in['category_id']) ; 
        }
        //配送方式
        if(isset($in['lgst_method'])){
            $Products->where('lgst_method' , $in['lgst_method']) ; 
        }
        //商品類型
        if(isset($in['product_type'])){
            $Products->where('product_type' , $in['product_type']) ; 
        }
        //上架狀態
        if(isset($in['approval_status'])){
            $Products->where('approval_status' , $in['approval_status']) ; 
        }
        //上架 下架時間 
        if(isset($in['select_start_date']) && isset($in['select_end_date'])){
            $select_start_date = $in['select_start_date'] . ' 00:00:00' ; 
            $select_end_date = $in['select_end_date'] . ' 23:59:59'; 
            $Products->whereDate('start_launched_at' , '<=' ,$select_start_date)
                     ->whereDate('end_launched_at' , '>=' ,$select_end_date);
        }
        //筆數
        if(isset($in['limit'])){
            $Products->limit($in['limit']) ; 
        }

        $result = $Products->get();

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
                'spec_1' => $in['spec_1'],
                'spec_2' => $in['spec_2'],
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
                    'product_id' => $products_id,
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
            $fileList = [];
            $uploadPath = '/products/' . $products->id;
            foreach ($file['filedata'] as $key => $val) {
                $fileList[$key] = ImageUpload::uploadImage($val, $uploadPath);
            }
            foreach ($fileList as $key => $val) {
                $insertImg = [
                    'product_id' => $products->id,
                    'photo_name' => $val['image'],
                    'sort' => $key,
                    'created_by' => $user_id,
                    'updated_by' => $user_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                ProductPhotos::create($insertImg) ; 
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

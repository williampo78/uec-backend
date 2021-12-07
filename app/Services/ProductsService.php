<?php

namespace App\Services;

use App\Models\ProductItems;
use App\Models\ProductPhotos;
use App\Models\Products;
use App\Models\Product_items;
use App\Services\UniversalService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use ImageUpload;

class ProductsService
{
    private $universalService;

    public function __construct()
    {
        $this->universalService = new UniversalService;
    }

    public function getProducts($input_data = [])
    {
        $agent_id = Auth::user()->agent_id;

        $products = Products::select('products.*', 'supplier.name AS supplier_name')
            ->leftJoin('supplier', 'products.supplier_id', '=', 'supplier.id')
            ->where('products.agent_id', $agent_id);

        //庫存類型
        if (isset($input_data['stock_type'])) {
            $products->where('products.stock_type', '=', $input_data['stock_type']);
        }

        //商品序號
        if (isset($input_data['product_no'])) {
            $product_no = explode(',', $input_data['product_no']);
            $product_no = array_unique($product_no);

            foreach ($product_no as $key => $val) {
                if ($key == 0) {
                    $products->where('products.product_no', 'like', '%' . $val . '%');
                } else {
                    $products->orWhere('products.product_no', 'like', '%' . $val . '%');
                }
            }
        }

        //供應商
        if (isset($input_data['supplier_id'])) {
            $products->where('products.supplier_id', $input_data['supplier_id']);
        }

        //商品通路
        if (isset($input_data['selling_channel'])) {
            $products->where('products.selling_channel', $input_data['selling_channel']);
        }

        //商品名稱
        if (isset($input_data['product_name'])) {
            $products->where('products.product_name', 'like', '%' . $input_data['product_name'] . '%');
        }

        //前台分類
        if (isset($input_data['category_id'])) {
            $products->where('products.category_id', $input_data['category_id']);
        }

        //配送方式
        if (isset($input_data['lgst_method'])) {
            $products->where('products.lgst_method', $input_data['lgst_method']);
        }

        //商品類型
        if (isset($input_data['product_type'])) {
            $products->where('products.product_type', $input_data['product_type']);
        }

        //上架狀態
        if (isset($input_data['approval_status'])) {
            $products->where('products.approval_status', $input_data['approval_status']);
        }

        try {
            // 上架起始日
            if (!empty($input_data['start_launched_at'])) {
                $start_launched_at = Carbon::parse($input_data['start_launched_at'])->format('Y-m-d H:i:s');
                $products->whereDate('products.start_launched_at', '>=', $start_launched_at);
            }

            // 上架結束日
            if (!empty($input_data['end_launched_at'])) {
                $end_launched_at = Carbon::parse($input_data['end_launched_at'])->format('Y-m-d H:i:s');
                $end_launched_at = $end_launched_at . ' 23:59:59';
                $products->whereDate('products.end_launched_at', '<=', $end_launched_at);
            }
        } catch (\Carbon\Exceptions\InvalidFormatException $e) {
            Log::warning($e->getMessage());
        }

        // 最低售價
        if (isset($input_data['selling_price_min'])) {
            $products->where('products.selling_price', '>=', $input_data['selling_price_min']);
        }

        // 最高售價
        if (isset($input_data['selling_price_max'])) {
            $products->where('products.selling_price', '<=', $input_data['selling_price_max']);
        }

        try {
            // 建檔日起始日期
            if (!empty($input_data['start_created_at'])) {
                $start_created_at = Carbon::parse($input_data['start_created_at'])->format('Y-m-d H:i:s');
                $products->where('products.created_at', '>=', $start_created_at);
            }

            // 建檔日結束日期
            if (!empty($input_data['end_created_at'])) {
                $end_created_at = Carbon::parse($input_data['end_created_at'])->format('Y-m-d H:i:s');
                $products->where('products.created_at', '<=', $end_created_at);
            }
        } catch (\Carbon\Exceptions\InvalidFormatException $e) {
            Log::warning($e->getMessage());
        }

        //限制筆數
        if (isset($input_data['limit'])) {
            $products->limit($input_data['limit']);
        }

        $result = $products->get();

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
                ProductPhotos::create($insertImg);
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
    public function showProducts($id)
    {
        $agent_id = Auth::user()->agent_id;
        $products = Products::where('agent_id', $agent_id)->where('id', $id);
        $result = $products->first();
        return $result;
    }

    public function getProductItems($products_id)
    {
        $agent_id = Auth::user()->agent_id;
        $product_items = Product_items::where('agent_id', $agent_id)->where('product_id', $products_id);
        $result = $product_items->get();
        return $result;
    }
    public function getProductsPhoto($products_id)
    {
        $ProductPhotos = ProductPhotos::where('product_id', $products_id);
        $result = $ProductPhotos->get();
        return $result;
    }
    public function getProductSpac($products_id)
    {
        $agent_id = Auth::user()->agent_id;
        $sql_spac_1 = '
        select distinct spec_1_value
        from( select
        sort , spec_1_value
        from product_items
        where product_id = ' . $products_id . '
        order by sort ) spac_1_table ';

        $sql_spac_2 = 'select distinct spec_1_value
        from( select
        sort , spec_1_value
        from product_items
        where product_id = ' . $products_id . '
        order by sort ) spac_1_table
        ';
        $result = [] ;
        $result['spac_1'] = DB::select($sql_spac_1);
        $result['spac_2']  = DB::select($sql_spac_2);
        return $result ;

    }
}

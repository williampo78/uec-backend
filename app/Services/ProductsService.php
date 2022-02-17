<?php

namespace App\Services;

use App\Models\CategoryHierarchy;
use App\Models\CategoryProducts;
use App\Models\ProductAttributes;
use App\Models\ProductAuditLog;
use App\Models\ProductItems;
use App\Models\ProductPhotos;
use App\Models\ProductReviewLog;
use App\Models\Products;
use App\Models\Product_spec_info;
use App\Models\RelatedProducts;
use App\Services\UniversalService;
use Batch;
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
        $now = Carbon::now();
        $products = Products::select(
            'products.*',
            'supplier.name AS supplier_name',
            DB::raw('get_latest_product_cost(products.id, TRUE) AS item_cost'),
            )
            ->leftJoin('supplier', 'products.supplier_id', '=', 'supplier.id')
            ->where('products.agent_id', $agent_id);

        if (isset($input_data['web_category_hierarchy_id'])) {
            $web_category_hierarchy_id = $input_data['web_category_hierarchy_id'];
            $products->join('web_category_products', function ($join) use ($web_category_hierarchy_id) {
                $join->where('web_category_products.web_category_hierarchy_id', '=', $web_category_hierarchy_id)
                    ->on('web_category_products.product_id', '=', 'products.id');
            });
        }
//庫存類型
        if (isset($input_data['id'])) {
            $products->where('products.id', '=', $input_data['id']);
        }
        //庫存類型
        if (isset($input_data['stock_type'])) {
            $products->where('products.stock_type', '=', $input_data['stock_type']);
        }

        //商品序號
        if (isset($input_data['product_no'])) {
            $product_no = explode(',', $input_data['product_no']);
            $product_no = array_unique($product_no);

            if (!empty($product_no)) {
                $products->where(function ($query) use ($product_no) {
                    foreach ($product_no as $val) {
                        $query->orWhere('products.product_no', 'like', '%' . $val . '%');
                    }
                });
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
            switch ($input_data['approval_status']) {
                //商品上架
                case 'APPROVED_STATUS_ON':
                    $products = $products->where(function ($query) use ($now) {
                        $query->where('products.approval_status', '=', 'APPROVED')
                            // ->whereRaw('current_timestamp between products.start_launched_at  and products.end_launched_at');
                            ->where('products.start_launched_at', '<=', $now)
                            ->where('products.end_launched_at', '>=', $now);
                    });
                    // products.approval_status = 'APPROVED' and current_timestamp between products.start_launched_at  and products.end_launched_at
                    break;
                //商品下架
                case 'APPROVED_STATUS_OFF':
                    $products = $products->where(function ($query) use ($now) {
                        $query->where('products.approval_status', '=', 'APPROVED')
                            ->where('products.start_launched_at', '>', $now)
                            ->orWhere('products.end_launched_at', '<', $now);
                    });
                    $products = $products->where(function ($query) {
                        $query->orWhere('products.approval_status', '=', 'CANCELLED');
                    });
                    // 狀況1 (  products.approval_status = 'CANCELLED'  )
                    // 狀況2 (  products.approval_status = 'APPROVED' and (current_timestamp < start_launched_at or current_timestamp > end_launched_at)  )
                    break;
                default:
                    $products->where('products.approval_status', '=', $input_data['approval_status']);
                    break;
            }
        }

        //上架起開始時間
        if (!empty($input_data['start_launched_at_start'])) {
            $products->whereDate('products.start_launched_at', '>=', $input_data['start_launched_at_start']);
        }

        //上架起結束時間
        if (!empty($input_data['start_launched_at_end'])) {
            $products->whereDate('products.start_launched_at', '<=', $input_data['start_launched_at_end']);
        }

        // 最低售價
        if (isset($input_data['selling_price_min'])) {
            $products->where('products.selling_price', '>=', $input_data['selling_price_min']);
        }

        // 最高售價
        if (isset($input_data['selling_price_max'])) {
            $products->where('products.selling_price', '<=', $input_data['selling_price_max']);
        }

        // 建檔日起始日期
        if (!empty($input_data['start_created_at'])) {
            $products->whereDate('products.created_at', '>=', $input_data['start_created_at']);
        }

        // 建檔日結束日期
        if (!empty($input_data['end_created_at'])) {
            $products->whereDate('products.created_at', '<=', $input_data['end_created_at']);
        }

        //限制筆數
        if (isset($input_data['limit'])) {
            $products->limit($input_data['limit']);
        }

        $products = $products->get();

        return $products;
    }

    public function addProducts($in, $file)
    {
        $user_id = Auth::user()->id;
        $agent_id = Auth::user()->agent_id;
        $now = Carbon::now();
        $skuList = json_decode($in['SkuListdata'], true);
        $specListJson = json_decode($in['SpecListJson'], true);
        DB::beginTransaction();
        try {
            $insert = [
                'stock_type' => $in['stock_type'],
                'product_no' => '',
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
                'spec_1' => $in['spec_1'] ?? '',
                'spec_2' => $in['spec_2'] ?? '',
                'selling_channel' => $in['selling_channel'],
                'delivery_type' => $in['delivery_type'],
                'created_by' => $user_id,
                'updated_by' => $user_id,
                'created_at' => $now,
                'updated_at' => $now,
                'agent_id' => $agent_id,
            ];
            $products_id = Products::create($insert)->id;
            $product_no = $this->universalService->getDocNumber('products', ['stock_type' => $in['stock_type'], 'id' => $products_id]);
            Products::where('id', $products_id)->update(['product_no' => $product_no]);
            $add_item_no = 1;
            foreach ($skuList as $key => $val) {
                $skuList[$key]['safty_qty'] = ltrim($val['safty_qty'], '0');
                $skuInsert = [
                    'agent_id' => $agent_id,
                    'product_id' => $products_id,
                    'sort' => $val['sort'] ?? 0,
                    'spec_1_value' => $val['spec_1_value'] ?? '',
                    'spec_2_value' => $val['spec_2_value'] ?? '',
                    'item_no' => $product_no . str_pad($add_item_no, 4, "0", STR_PAD_LEFT), //新增時直接用key生成id
                    'supplier_item_no' => $val['supplier_item_no'],
                    'ean' => $val['ean'],
                    'pos_item_no' => $val['pos_item_no'],
                    'safty_qty' => $skuList[$key]['safty_qty'],
                    'is_additional_purchase' => $val['is_additional_purchase'],
                    'status' => $val['status'],
                    'created_by' => $user_id,
                    'updated_by' => $user_id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $skuList[$key]['id'] = ProductItems::create($skuInsert)->id;
                $skuList[$key]['item_no'] = $product_no . str_pad($add_item_no, 4, "0", STR_PAD_LEFT);
                $add_item_no += 1;
            }
            Product_spec_info::create([
                'product_id' => $products_id,
                'spec_value_list' => json_encode($specListJson),
                'item_list' => json_encode($skuList),
                'created_by' => $user_id,
                'updated_by' => $user_id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $fileList = [];
            $uploadPath = '/products/' . $products_id;
            if (isset($file['filedata'])) {
                foreach ($file['filedata'] as $key => $val) {
                    $fileList[$key] = ImageUpload::uploadImage($val, $uploadPath);
                }
                foreach ($fileList as $key => $val) {
                    $insertImg = [
                        'product_id' => $products_id,
                        'photo_name' => $val['image'],
                        'sort' => $key,
                        'created_by' => $user_id,
                        'updated_by' => $user_id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    ProductPhotos::create($insertImg);
                }
            }
            DB::commit();
            $result['status'] = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::warning($e->getMessage());
            $result['error_code'] = $e->getMessage();
            $result['status'] = false;
        }
        return $result;
    }

    public function editProducts($in, $file)
    {
        $products_id = $in['id'];
        $user_id = Auth::user()->id;
        $agent_id = Auth::user()->agent_id;
        $now = Carbon::now();
        $skuList = json_decode($in['SkuListdata'], true);
        $specListJson = json_decode($in['SpecListJson'], true);
        $imgJson = json_decode($in['imgJson'], true);
        DB::beginTransaction();
        try {
            $update = [
                'stock_type' => $in['stock_type'],
                // 'supplier_id' => $in['supplier_id'],
                'product_name' => $in['product_name'],
                'tax_type' => $in['tax_type'],
                // 'category_id' => $in['category_id'],
                // 'brand_id' => $in['brand_id'],
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
                // 'spec_1' => $in['spec_1'] ?? '',
                // 'spec_2' => $in['spec_2'] ?? '',
                'selling_channel' => $in['selling_channel'],
                'delivery_type' => $in['delivery_type'],
                'updated_by' => $user_id,
                'updated_at' => $now,
            ];
            if (isset($in['supplier_id']) && $in['supplier_id'] !== '') {
                $update['supplier_id'] = $in['supplier_id'];
            }
            if (isset($in['category_id']) && $in['category_id'] !== '') {
                $update['category_id'] = $in['category_id'];
            }
            if (isset($in['brand_id']) && $in['brand_id'] !== '') {
                $update['brand_id'] = $in['brand_id'];
            }

            Products::where('id', $products_id)->update($update);
            $logCreateIn = [
                'product_id' => $products_id,
                'created_by' => $user_id,
                'updated_by' => $user_id,
            ];
            ProductAuditLog::create($logCreateIn);
            $uploadPath = '/products/' . $products_id;
            foreach ($imgJson as $key => $val) {
                if (isset($val['id'])) {
                    $updateImg = [
                        'sort' => $key,
                        'updated_by' => $user_id,
                        'updated_at' => $now,
                    ];
                    ProductPhotos::where('id', $val['id'])->update($updateImg);
                } else {
                    $ImageUpload = ImageUpload::uploadImage($file['filedata'][$key], $uploadPath);
                    $insertImg = [
                        'product_id' => $products_id,
                        'photo_name' => $ImageUpload['image'],
                        'sort' => $key,
                        'created_by' => $user_id,
                        'updated_by' => $user_id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    ProductPhotos::create($insertImg);
                }
            }
            $add_item_no = ProductItems::where('product_id', $products_id)->count();
            foreach ($skuList as $key => $val) {
                $skuList[$key]['safty_qty'] = ltrim($val['safty_qty'], '0');
                if ($val['id'] == '') {
                    $add_item_no += 1;
                    $skuInsert = [
                        'agent_id' => $agent_id,
                        'product_id' => $products_id,
                        'sort' => $val['sort'] ?? 0,
                        'spec_1_value' => $val['spec_1_value'] ?? '',
                        'spec_2_value' => $val['spec_2_value'] ?? '',
                        'item_no' => $in['product_no'] . str_pad($add_item_no, 4, "0", STR_PAD_LEFT), //新增時直接用key生成id
                        'supplier_item_no' => $val['supplier_item_no'],
                        'ean' => $val['ean'],
                        'pos_item_no' => $val['pos_item_no'],
                        'safty_qty' => $skuList[$key]['safty_qty'],
                        'is_additional_purchase' => $val['is_additional_purchase'],
                        'status' => $val['status'],
                        'edi_exported_status' => null,
                        'created_by' => $user_id,
                        'updated_by' => $user_id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                    $skuList[$key]['id'] = ProductItems::create($skuInsert)->id;
                    $skuList[$key]['item_no'] = $in['product_no'] . str_pad($add_item_no, 4, "0", STR_PAD_LEFT);
                } else {
                    $skuUpdate = [
                        'agent_id' => $agent_id,
                        'product_id' => $products_id,
                        'sort' => $val['sort'] ?? 0,
                        'spec_1_value' => $val['spec_1_value'] ?? '',
                        'spec_2_value' => $val['spec_2_value'] ?? '',
                        'supplier_item_no' => $val['supplier_item_no'],
                        'ean' => $val['ean'],
                        'pos_item_no' => $val['pos_item_no'],
                        'safty_qty' => $skuList[$key]['safty_qty'],
                        'is_additional_purchase' => $val['is_additional_purchase'],
                        'status' => $val['status'],
                        'updated_by' => $user_id,
                        'updated_at' => $now,
                        'edi_exported_status' => null,
                    ];
                    ProductItems::where('id', $val['id'])->update($skuUpdate);
                }

            }
            Product_spec_info::where('product_id', $products_id)->update([
                'spec_value_list' => json_encode($specListJson),
                'item_list' => json_encode($skuList),
                'updated_by' => $user_id,
                'updated_at' => $now,
            ]);
            DB::commit();
            $result['status'] = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::warning($e->getMessage());
            $result['status'] = false;
            $result['error_code'] = $e->getMessage();
        }
        return $result;
    }

    public function showProducts($id)
    {
        $agent_id = Auth::user()->agent_id;
        $products = Products::select(
            'products.*',
            'updated_by_name.user_name AS updated_name',
            'created_by_name.user_name AS created_name',
            'created_by_name.id as created_by_name_id',
            'updated_by_name.id as updated_by_name_id',
            'supplier.name AS supplier_name',
            'products_v.gross_margin',
            'products_v.item_cost'
        )
            ->leftJoin('users as created_by_name', 'products.created_by', '=', 'created_by_name.id')
            ->leftJoin('users as updated_by_name', 'products.updated_by', '=', 'updated_by_name.id')
            ->leftJoin('products_v', 'products_v.id', '=', 'products.id')
            ->leftJoin('supplier', 'products.supplier_id', '=', 'supplier.id')
            ->where('products.agent_id', $agent_id)->where('products.id', $id);
        $result = $products->first();
        return $result;
    }

    public function getProductItems($products_id)
    {
        $agent_id = Auth::user()->agent_id;
        $ProductItems = ProductItems::where('agent_id', $agent_id)->where('product_id', $products_id);
        $result = $ProductItems->get();
        return $result;
    }

    public function getItemsAndProduct($in = [])
    {
        $agent_id = Auth::user()->agent_id;
        $ProductItems = ProductItems::
        select('product_items.*', 'products.product_name', 'products.brand_id', 'products.min_purchase_qty', 'products.uom', 'brands.brand_name as brand_name')
            ->where('product_items.agent_id', $agent_id)
            ->leftJoin('products', 'products.id', '=', 'product_items.product_id')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id');
        if (isset($in['supplier_id']) && $in['supplier_id'] !== '') {
            $ProductItems->where('products.supplier_id', $in['supplier_id']);
        }

        $result = $ProductItems->get();
        return $result;
    }

    public function getProductsPhoto($products_id)
    {
        $ProductPhotos = ProductPhotos::where('product_id', $products_id)->orderBy('sort', 'ASC');
        $results = $ProductPhotos->get();
        $results = $results->map(function ($result) {
            $result->photo_size = ImageUpload::getSize($result->photo_name);
            return $result;
        });
        return $results;
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
        $result = [];
        $result['spac_1'] = DB::select($sql_spac_1);
        $result['spac_2'] = DB::select($sql_spac_2);
        return $result;

    }

    /**
     * 重組商品資料
     *
     * @param collection $products
     * @return collection
     */
    public function restructureProducts($products)
    {
        $products->transform(function ($product) {
            // 上架日期
            $product->launched_at = ($product->start_launched_at || $product->end_launched_at) ? "{$product->start_launched_at} ~ {$product->end_launched_at}" : '';

            // 毛利
            $product->gross_margin = (isset($product->item_cost, $product->selling_price) && $product->selling_price != 0) ? round(((1 - ($product->item_cost / $product->selling_price)) * 100), 2) : null;

            // 售價
            $product->selling_price = number_format($product->selling_price);

            // 上架狀態
            switch ($product->approval_status) {
                case 'NA':
                    $product->launched_status = '未設定';
                    break;

                case 'REVIEWING':
                    $product->launched_status = '上架申請';
                    break;

                case 'REJECTED':
                    $product->launched_status = '上架駁回';
                    break;

                case 'CANCELLED':
                    $product->launched_status = '商品下架';
                    break;

                case 'APPROVED':
                    $product->launched_status = Carbon::now()->between($product->start_launched_at, $product->end_launched_at) ? '商品上架' : '商品下架';
                    break;
            }

            return $product;
        });
    }

    public function getProduct_spec_info($product_id)
    {
        $result = Product_spec_info::where('product_id', $product_id)->first();
        return $result;
    }

    public function getRelatedProducts($product_id)
    {
        $result = RelatedProducts::select('related_products.*', 'products.product_name')
            ->where('related_products.product_id', $product_id)
            ->leftJoin('products', 'products.id', '=', 'related_products.related_product_id')
            ->orderBy('related_products.sort', 'ASC')
            ->get();
        // dd($result) ;
        return $result;
    }

    public function updateProductSmall($in, $file = array(), $id)
    {
        $result = [];
        $user_id = Auth::user()->id;
        $agent_id = Auth::user()->agent_id;
        $now = Carbon::now();
        $CategoryHierarchyProducts = json_decode($in['CategoryHierarchyProducts_Json'], true);
        $RelatedProducts = json_decode($in['RelatedProducts_Json'], true);
        $ProductsItem = json_decode($in['ProductsItem_Json'], true);
        DB::beginTransaction();
        try {
            $updateIn = [
                'stock_type' => $in['stock_type'],
                'product_name' => $in['product_name'],
                'keywords' => $in['keywords'],
                'order_limited_qty' => $in['order_limited_qty'],
                'promotion_desc' => $in['promotion_desc'],
                'promotion_start_at' => $in['promotion_start_at'],
                'promotion_end_at' => $in['promotion_end_at'],
                'description' => $in['description'],
                'specification' => $in['specification'],
                'meta_title' => $in['meta_title'],
                'mata_description' => $in['mata_description'],
                'mata_keywords' => $in['mata_keywords'],
                'updated_by' => $user_id,
            ];
            if (isset($file['google_shop_photo_name'])) {
                if ($in['google_shop_photo_name_old'] !== null) {
                    ImageUpload::DelPhoto($in['google_shop_photo_name_old']);
                }
                $uploadPath = '/products/' . $id;
                $uploadImage = ImageUpload::uploadImage($file['google_shop_photo_name'], $uploadPath);
                $updateIn['google_shop_photo_name'] = $uploadImage['image'];
            }
            Products::where('id', $id)->update($updateIn);
            $logCreateIn = [
                'product_id' => $id,
                'created_by' => $user_id,
                'updated_by' => $user_id,
            ];
            ProductAuditLog::create($logCreateIn);
            if (count($ProductsItem) > 0) {
                $ProductItemsInstance = new ProductItems();
                foreach ($ProductsItem as $key => $val) {
                    $ProductsItemUpdate[$key] = [
                        'id' => $val['id'],
                        'photo_name' => $val['photo_name'],
                    ];
                }
                $upd = Batch::update($ProductItemsInstance, $ProductsItemUpdate, 'id');
            }
            foreach ($CategoryHierarchyProducts as $key => $val) {
                if ($val['status'] == 'new') {
                    CategoryProducts::create([
                        'web_category_hierarchy_id' => $val['web_category_hierarchy_id'],
                        'product_id' => $id,
                        'sort' => $key,
                        'created_by' => $user_id,
                        'updated_by' => $user_id,
                    ]);
                } else { // status old
                    DB::table('web_category_products')
                        ->where('web_category_hierarchy_id', $val['web_category_hierarchy_id'])
                        ->where('product_id', $id)
                        ->update([
                            'sort' => $key,
                            'updated_by' => $user_id,
                        ]);
                }
            }
            foreach ($RelatedProducts as $key => $val) {
                if ($val['id'] == '') {
                    $in = [
                        'product_id' => $id,
                        'related_product_id' => $val['related_product_id'],
                        'sort' => $key,
                        'created_by' => $user_id,
                        'updated_by' => $user_id,
                    ];
                    RelatedProducts::create($in);
                } else {
                    RelatedProducts::where('product_id', $val['product_id'])
                        ->where('related_product_id', $val['related_product_id'])
                        ->update([
                            'sort' => $key,
                            'updated_by' => $user_id,
                        ]);
                }
            }
            if (isset($file['photo_name'])) {

                foreach ($file['photo_name'] as $key => $val) {
                    $uploadPath = '/product_items/' . $id;
                    if ($ProductsItem[$key]['photo_name'] !== '') {
                        ImageUpload::DelPhoto($ProductsItem[$key]['photo_name']);
                    }
                    $uploadImage = ImageUpload::uploadImage($val, $uploadPath);

                    ProductItems::where('id', $ProductsItem[$key]['id'])->update(
                        [
                            'photo_name' => $uploadImage['image'],
                            'updated_by' => $user_id,
                        ]
                    );
                };
            }
            if (isset($in['product_attributes_change']) && $in['product_attributes_change'] == 'true') {
                ProductAttributes::where('attribute_type', 'CERTIFICATE')->where('product_id', $id)->delete();
                $add_product_attributes = [];
                if (isset($in['product_attributes'])) {
                    foreach ($in['product_attributes'] as $key => $val) {
                        $add_product_attributes[$key]['attribute_type'] = 'CERTIFICATE';
                        $add_product_attributes[$key]['product_attribute_lov_id'] = $val;
                        $add_product_attributes[$key]['product_id'] = $id;
                        $add_product_attributes[$key]['created_by'] = $user_id;
                        $add_product_attributes[$key]['updated_by'] = $user_id;
                        $add_product_attributes[$key]['created_at'] = $now;
                        $add_product_attributes[$key]['updated_at'] = $now;
                    }
                    ProductAttributes::insert($add_product_attributes);
                }
            }
            $result['status'] = true;
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::warning($e->getMessage());
            $result['status'] = false;
            $result['error_code'] = $e->getMessage();
        }
        return $result;
    }

    public function getProductAuditLog($product_id)
    {
        $ProductAuditLog = ProductAuditLog::select('product_audit_log.*', 'users.user_name AS user_name')
            ->orderBy('product_audit_log.updated_at', 'DESC')
            ->leftJoin('users', 'users.id', '=', 'product_audit_log.updated_by')
            ->where('product_audit_log.product_id', $product_id)
            ->get();
        return $ProductAuditLog;
    }

    public function getProductReviewLog($id)
    {
        $getProductReviewLog = ProductReviewLog::select('product_review_log.*', 'discontinued_user.user_name AS discontinued_user_name')
            ->orderBy('product_review_log.updated_at', 'DESC')
            ->leftJoin('users as discontinued_user', 'discontinued_user.id', '=', 'product_review_log.discontinued_by')
            ->where('product_review_log.product_id', $id)
            ->get();
        return $getProductReviewLog;
    }

    //申請審核
    public function addProductReviewLog($in, $product_id)
    {
        $user_id = Auth::user()->id;
        DB::beginTransaction();
        try {
            ProductItems::where('product_id', $product_id)->update(['edi_exported_status' => null]);
            ProductAuditLog::create([
                'product_id' => $product_id,
                'created_by' => $user_id,
                'updated_by' => $user_id,
            ]);
            Products::where('id', $product_id)->update([
                'start_launched_at' => $in['start_launched_at'],
                'end_launched_at' => $in['end_launched_at'],
                'approval_status' => 'REVIEWING',
                'updated_by' => $user_id,
            ]);
            ProductReviewLog::create([
                'product_id' => $product_id,
                'selling_price' => $in['selling_price'],
                'start_launched_at' => $in['start_launched_at'],
                'end_launched_at' => $in['end_launched_at'],
                'created_by' => $user_id,
                'updated_by' => $user_id,
            ]);
            DB::commit();
            $result = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::warning($e->getMessage());
            $result = false;
        }
        return $result;
    }

    //審核
    public function addProductReview($in, $id)
    {
        if ($in['review_result'] == '1') { //允許
            $review_result = 'APPROVE';
            $approval_status = 'APPROVED';
        } else {
            $review_result = 'REJECT';
            $approval_status = 'REJECTED';
        }
        $user_id = Auth::user()->id;
        $now = Carbon::now();
        DB::beginTransaction();
        try {
            ProductReviewLog::where('product_id', $id)->orderBy('id', 'DESC')->first()->update([
                'review_result' => $review_result,
                'review_remark' => $in['review_remark'],
                'reviewer' => $user_id,
                'review_at' => $now,
                'updated_by' => $user_id,
            ]);
            Products::where('id', $id)->update([
                'approval_status' => $approval_status,
                'updated_by' => $user_id,
            ]);
            DB::commit();
            $result = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::warning($e->getMessage());
            $result = false;
        }
        return $result;
    }

    /**
     * 商品上下架商品
     *
     */
    public function offProduct($in)
    {
        $user_id = Auth::user()->id;
        $now = Carbon::now();
        DB::beginTransaction();
        try {
            $count_review = ProductReviewLog::where('product_id', $in['product_id'])->orderBy('id', 'DESC')->count();
            if ($count_review > 0) {
                ProductReviewLog::where('product_id', $in['product_id'])->orderBy('id', 'DESC')->first()->update([
                    'discontinued_by' => $user_id,
                    'discontinued_at' => $now,
                    'updated_by' => $user_id,
                ]);
            }
            Products::where('id', $in['product_id'])->update([
                'approval_status' => 'CANCELLED',
                'updated_by' => $user_id,
                'end_launched_at' => $now,
            ]);
            DB::commit();
            $result = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::warning($e->getMessage());
            $result = false;
        }
        return $result;
    }

    /**
     *
     * 點擊「送審」時，若商品為「一般品 (products.product_type='N')」，
     * 需檢查該商品是否有存在的前台分類，若無，顯示錯誤訊息：商品未完成「商城資料」維護，不允許執行上架送審
     */
    public function checkProductReady($in)
    {
        $count = CategoryHierarchy::select('web_category_products.*')
            ->Join('web_category_products', 'web_category_products.web_category_hierarchy_id', 'web_category_hierarchy.id')
            ->where('web_category_products.product_id', $in['product_id'])
            ->where('web_category_hierarchy.active', '1')->count();
        if ($count == 0) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * 確認 PosItemNo 是否有重複
     *
     */
    public function checkPosItemNo($PosItemNo, $ItemNo)
    {
        if ($ItemNo !== '') { //編輯才會進來這裡檢查是否是自己的 pos_item_no
            $updateCheck = ProductItems::where('pos_item_no', $PosItemNo)->where('item_no', $ItemNo);
            if ($updateCheck->count() > 0) {
                return true;
            }
        }

        $ProductItems = ProductItems::where('pos_item_no', $PosItemNo);
        if ($ProductItems->count() > 0) { //已存在
            return false;
        } else {
            return true;
        }

    }

    /**
     * 由products id 刪除 google_shop_photo_name
     */
    public function delGoogleShopPhoto($products_id)
    {
        $Products = Products::where('id', $products_id)->first();
        ImageUpload::DelPhoto($Products->google_shop_photo_name);
        $user_id = Auth::user()->id;
        $Products->update([
            'google_shop_photo_name' => '',
            'updated_by' => $user_id,
        ]);
        return true;
    }

    /**
     * 由products id 刪除 google_shop_photo_name
     */
    public function delItemPhotos($item_id)
    {
        $user_id = Auth::user()->id;

        $Products = Products::where('id', $item_id)->first();

        ProductItems::where('id', $item_id)->update([
            'photo_name' => '',
            'updated_by' => $user_id,
        ]);

        return true;
    }

    /**
     * 取得商品 items
     *
     */
    public function getItemsJoinProducts($request)
    {
        $agent_id = Auth::user()->agent_id;
        $now = Carbon::now();
        $query = ProductItems::select(
            DB::raw('product_items.*'),
            DB::raw('products.*'),
            DB::raw('supplier.name as supplier_name'), //供應商
            DB::raw('brands.brand_name as brand_name'),
            DB::raw('products_v.item_cost as item_cost'),
            DB::raw('products_v.gross_margin as gross_margin'),
            DB::raw('(select group_concat(wch.category_name)
            from web_category_products wcp
            join web_category_hierarchy wch on wch.id = wcp.web_category_hierarchy_id
           where wcp.product_id = products.id
           order by wcp.sort) as web_category_products_category_name'),
            DB::raw('(select group_concat(p.product_name)
            from related_products rp
            join products p on p.id = rp.related_product_id
           where rp.product_id = products.id
           order by rp.sort) as related_product_name'),
            )
            ->leftJoin('products', 'products.id', 'product_items.product_id')
            ->leftJoin('products_v', 'products_v.id', 'product_items.product_id')
            ->leftJoin('supplier', 'products.supplier_id', '=', 'supplier.id')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->groupBy('product_items.id');
        if (isset($request['web_category_hierarchy_id'])) {
            $web_category_hierarchy_id = $request['web_category_hierarchy_id'];
            $query->join('web_category_products', function ($join) use ($web_category_hierarchy_id) {
                $join->where('web_category_products.web_category_hierarchy_id', '=', $web_category_hierarchy_id)
                    ->on('web_category_products.product_id', '=', 'products.id');
            });
        }

        //庫存類型
        if (isset($request['stock_type'])) {
            $query = $query->where('products.stock_type', '=', $request['stock_type']);
        }

        //商品序號
        if (isset($request['product_no'])) {
            $product_no = explode(',', $request['product_no']);
            $product_no = array_unique($product_no);

            foreach ($product_no as $key => $val) {
                if ($key == 0) {
                    $query = $query->where('products.product_no', 'like', '%' . $val . '%');
                } else {
                    $query = $query->orWhere('products.product_no', 'like', '%' . $val . '%');
                }
            }
        }

        //供應商
        if (isset($request['supplier_id'])) {
            $query = $query->where('products.supplier_id', $request['supplier_id']);
        }

        //商品通路
        if (isset($request['selling_channel'])) {
            $query = $query->where('products.selling_channel', $request['selling_channel']);
        }

        //商品名稱
        if (isset($request['product_name'])) {
            $query = $query->where('products.product_name', 'like', '%' . $request['product_name'] . '%');
        }

        //前台分類
        if (isset($request['category_id'])) {
            $query = $query->where('products.category_id', $request['category_id']);
        }

        //配送方式
        if (isset($request['lgst_method'])) {
            $query = $query->where('products.lgst_method', $request['lgst_method']);
        }

        //商品類型
        if (isset($request['product_type'])) {
            $query = $query->where('products.product_type', $request['product_type']);
        }

        //上架狀態
        if (isset($request['approval_status'])) {
            switch ($request['approval_status']) {
                //商品上架
                case 'APPROVED_STATUS_ON':
                    $query = $query->where(function ($query) use ($now) {
                        $query->where('products.approval_status', '=', 'APPROVED')
                            ->where('products.start_launched_at', '<=', $now)
                            ->where('products.end_launched_at', '>=', $now);
                    });
                    break;
                //商品下架
                case 'APPROVED_STATUS_OFF':
                    $query = $query->where(function ($query) use ($now) {
                        $query->where('products.approval_status', '=', 'APPROVED')
                            ->where('products.start_launched_at', '>', $now)
                            ->orWhere('products.end_launched_at', '<', $now);
                    });
                    $query = $query->where(function ($query) {
                        $query->orWhere('products.approval_status', '=', 'CANCELLED');
                    });
                    break;
                default:
                    $query = $query->where('products.approval_status', '=', $request['approval_status']);
                    break;
            }
        }

        //上架起開始時間
        if (!empty($request['start_launched_at_start'])) {
            $query = $query->whereDate('products.start_launched_at', '>=', $request['start_launched_at_start']);
        }

        //上架起結束時間
        if (!empty($request['start_launched_at_end'])) {
            $query = $query->whereDate('products.start_launched_at', '<=', $request['start_launched_at_end']);
        }

        // 最低售價
        if (isset($request['selling_price_min'])) {
            $query = $query->where('products.selling_price', '>=', $request['selling_price_min']);
        }

        // 最高售價
        if (isset($request['selling_price_max'])) {
            $query = $query->where('products.selling_price', '<=', $request['selling_price_max']);
        }

        // 建檔日起始日期
        if (!empty($request['start_created_at'])) {
            $query = $query->whereDate('products.created_at', '>=', $request['start_created_at']);
        }

        // 建檔日結束日期
        if (!empty($request['end_created_at'])) {
            $query = $query->whereDate('products.created_at', '<=', $request['end_created_at']);
        }

        // //限制筆數
        if (isset($request['limit'])) {
            $query = $query->limit(1000);
        }

        $query = $query->get();

        return $query;

    }

    /**
     * 商品items整理
     *
     *
     */
    public function restructureItemsProducts($products, $pos)
    {
        $products->transform(function ($obj, $key) use ($pos) {

            // 上架日期
            $obj->launched_at = ($obj->start_launched_at || $obj->end_launched_at) ? "{$obj->start_launched_at} ~ {$obj->end_launched_at}" : '';

            // 毛利
            $obj->gross_margin = (isset($obj->item_cost, $obj->selling_price) && $obj->selling_price != 0) ? round(((1 - ($obj->item_cost / $obj->selling_price)) * 100), 2) : null;

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
                default:
                    $obj->launched_status = 'NULL';
                    break;
            }

            //庫存類型
            switch ($obj->stock_type) {
                case 'A':
                    $obj->stock_type_cn = '買斷 [A]';
                    break;
                case 'B':
                    $obj->stock_type_cn = '寄售 [B]';
                    break;
                case 'T':
                    $obj->stock_type_cn = '轉單 [T]';
                    break;
                default:
                    $obj->stock_type_cn = '庫存類型錯誤';
                    break;
            }
            //課稅別
            switch ($obj->tax_type) {
                case 'TAXABLE':
                    $obj->tax_type_cn = '應稅(5%)';
                    break;
                case 'NON_TAXABLE':
                    $obj->tax_type_cn = '免稅';
                    break;
                default:
                    $obj->tax_type_cn = '課稅別錯誤';
                    break;
            }
            //小分類找到中大分類
            if (isset($pos[$obj->category_id])) {
                $obj->primary_category = $pos[$obj->category_id]['primary_category']; //POS大分類
                $obj->category_name = $pos[$obj->category_id]['category_name']; //POS中分類
                $obj->tertiary_categories_name = $pos[$obj->category_id]['tertiary_categories_name']; //POS小分類
            } else {
                $obj->primary_category = '分類異常';
                $obj->category_name = '分類異常';
                $obj->tertiary_categories_name = '分類異常';
            }
            //溫層
            switch ($obj->lgst_temperature) {
                case 'NORMAL':
                    $obj->lgst_temperature_cn = '常溫';
                    break;
                default:
                    $obj->lgst_temperature_cn = '';
                    break;
            }
            //商品交期
            switch ($obj->lgst_method) {
                case 'HOME':
                    $obj->lgst_method_cn = '宅配';
                    break;
                case 'FAMILY':
                    $obj->lgst_method_cn = '全家取貨';
                    break;
                case 'STORE':
                    $obj->lgst_method_cn = '門市取貨';
                    break;
                default:
                    $obj->lgst_method_cn = '';
                    # code...
                    break;
            }
            //商品交期
            switch ($obj->delivery_type) {
                case 'IN_STOCK':
                    $obj->delivery_type_cn = '現貨';
                    break;
                case 'PRE_ORDER':
                    $obj->delivery_type_cn = '預購';
                    break;
                case 'CUSTOMIZED':
                    $obj->delivery_type_cn = '訂製';
                    break;
                default:
                    $obj->delivery_type_cn = '';
                    break;
            }
            //商品類別
            switch ($obj->product_type) {
                case 'N':
                    $obj->product_type_cn = '一般品';
                    break;
                case 'G':
                    $obj->product_type_cn = '贈品';
                    break;
                case 'A':
                    $obj->product_type_cn = '加購品';
                    break;
                default:
                    $obj->product_type_cn = '';
                    break;
            }
            switch ($obj->selling_channel) {
                case 'EC':
                    $obj->selling_channel_cn = '網路獨賣';
                    break;
                case 'STORE':
                    $obj->selling_channel_cn = '門市限定';
                    break;
                case 'WHOLE':
                    $obj->selling_channel_cn = '全通路';
                    break;
                default:
                    $obj->selling_channel_cn = '無';
                    break;
            }
            if ($obj->has_expiry_date) {
                $obj->has_expiry_date_cn = '有';
            } else {
                $obj->has_expiry_date_cn = '無';
            }
            //停售
            if ($obj->is_discontinued) {
                $obj->is_discontinued_cn = '是';
            } else {
                $obj->is_discontinued_cn = '否';
            }
            //是否有保固
            if ($obj->is_with_warranty) {
                $obj->is_with_warranty_cn = '有';
            } else {
                $obj->is_with_warranty_cn = '沒有';
            }
            //規格類型
            switch ($obj->spec_dimension) {
                case '0':
                    $obj->spec_dimension_cn = '單規格';
                    break;
                case '1':
                    $obj->spec_dimension_cn = '一維多規格';
                    break;
                case '2':
                    $obj->spec_dimension_cn = '二維多規格';
                    break;
                default:
                    $obj->spec_dimension_cn = '';
                    break;
            }
            //是否追加
            if ($obj->is_additional_purchase) {
                $obj->is_additional_purchase_cn = '是';
            } else {
                $obj->is_additional_purchase_cn = '否';
            }
            if ($obj->status) {
                $obj->status_cn = '是';
            } else {
                $obj->status_cn = '否';

            }
            return $obj;
        });
    }

}

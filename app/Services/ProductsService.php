<?php

namespace App\Services;

use App\Models\CategoryProducts;
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
            'supplier.name AS supplier_name'
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
                    'safty_qty' => $val['safty_qty'],
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
            $result = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::warning($e->getMessage());
            $result = false;
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
                // 'spec_1' => $in['spec_1'] ?? '',
                // 'spec_2' => $in['spec_2'] ?? '',
                'selling_channel' => $in['selling_channel'],
                'delivery_type' => $in['delivery_type'],
                'updated_by' => $user_id,
                'updated_at' => $now,
            ];
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
                        'safty_qty' => $val['safty_qty'],
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
                        'safty_qty' => $val['safty_qty'],
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
        $products = Products::select('products.*', 'updated_by_name.user_name AS updated_by_name', 'created_by_name.user_name AS created_by_name', 'supplier.name AS supplier_name')
            ->leftJoin('users as created_by_name', 'products.created_by', '=', 'created_by_name.id')
            ->leftJoin('users as updated_by_name', 'products.updated_by', '=', 'updated_by_name.id')
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
            select('product_items.*', 'products.product_name', 'products.brand_id', 'products.min_purchase_qty', 'products.uom','brands.brand_name as brand_name' )
            ->where('product_items.agent_id', $agent_id)
            ->leftJoin('products', 'products.id', '=', 'product_items.product_id')
            ->leftJoin('brands','brands.id' ,'=' ,'products.brand_id')
            ;
        if (isset($in['supplier_id']) && $in['supplier_id'] !== '') {
            $ProductItems->where('products.supplier_id', $in['supplier_id']);
        }

        $result = $ProductItems->get();
        return $result;
    }
    public function getProductsPhoto($products_id)
    {
        $ProductPhotos = ProductPhotos::where('product_id', $products_id);
        $results = $ProductPhotos->get();
        $results = $results->map(function ($result) {
            $result->photo_size = ImageUpload::getSize($result->photo_name);return $result;
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
        $products->transform(function ($obj, $key) {
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
            // dd($obj) ;
            return $obj;
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
            $ProductItemsInstance = new ProductItems();
            foreach ($ProductsItem as $key => $val) {
                $ProductsItemUpdate[$key] = [
                    'id' => $val['id'],
                    'photo_name' => $val['photo_name'],
                ];
            }
            $upd = Batch::update($ProductItemsInstance, $ProductsItemUpdate, 'id');

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
                    CategoryProducts::where('web_category_hierarchy_id', $val['web_category_hierarchy_id'])
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

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::warning($e->getMessage());
            return false;
        }
    }
    public function getProductAuditLog($product_id)
    {
        $ProductAuditLog = ProductAuditLog::select('product_audit_log.*', 'users.user_name AS user_name')
            ->orderBy('product_audit_log.updated_at', 'DESC')
            ->leftJoin('users', 'users.id', '=', 'product_audit_log.updated_by')
            ->get();
        return $ProductAuditLog;
    }
    public function getProductReviewLog($id)
    {
        $getProductReviewLog = ProductReviewLog::select('product_review_log.*', 'discontinued_user.user_name AS discontinued_user_name')
            ->orderBy('product_review_log.updated_at', 'DESC')
            ->leftJoin('users as discontinued_user', 'discontinued_user.id', '=', 'product_review_log.discontinued_by')
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
}

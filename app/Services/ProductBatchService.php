<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\ProductItem;
use Illuminate\Support\Str;
use App\Jobs\ProductImportJob;
use App\Models\BatchUploadLog;
use App\Services\BrandsService;
use App\Models\TertiaryCategory;
use App\Models\SupplierStockType;
use App\Services\SupplierService;
use App\Models\WebCategoryProduct;
use App\Services\UniversalService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Services\WebCategoryHierarchyService;

class ProductBatchService
{

    private $webCategoryHierarchyService;

    public function __construct(WebCategoryHierarchyService $webCategoryHierarchyService,
        SupplierService $supplierService,
        BrandsService $brandsService,
        UniversalService $universalService) {
        $this->webCategoryHierarchyService = $webCategoryHierarchyService;
        $this->supplierService = $supplierService;
        $this->brandsService = $brandsService;
        $this->universalService = $universalService;
    }

    /**
     * 儲存檔案 並回傳新的路徑以及檔名
     *
     * @param  [type] $file
     * @return void
     */
    public function saveFile($file)
    {
        $random = Str::random(10);
        $originalName = $file->getClientOriginalName();
        $originaType = $file->getClientOriginalExtension();
        $dateY = date('Y');
        $dateYmdHis = date('YmdHis');

        $fileName = "{$dateYmdHis}{$random}.{$originaType}";
        $filePath = env('BATCH_UPLOAD_PATH','uploads/batch/')."{$dateY}/{$random}/{$fileName}";

        Storage::put(
            $filePath,
            file_get_contents($file->getRealPath())
        );

        return [
            'originalName' => $originalName,
            'filePath' => $filePath,
        ];
    }
    /**
     * 匯入紀錄LOG
     *
     * @param  [type] $inputLogData
     * @return void
     */
    public function addBatchUploadLog($inputLogData)
    {
        try {
            $result = BatchUploadLog::create($inputLogData);

            return $result->id;
        } catch (\Exception $e) {
            Log::channel('batch_upload')->warning('addBatchUploadLog' . $e->getMessage());

            return false;
        }
    }

    /**
     * 依id更新狀態
     * 狀態為完成時，更新完成時間
     *
     * @param int $logId
     * @param int $status
     * @param array $content =
     * ['job_completed_log'=>'string'
     *   job_log_file' => 'string']
     * @return bool
     */
    public function updateStatusById(int $batchUploadLogId, int $status, array $content = []): bool
    {
        $batchUploadLog = $this->getById($batchUploadLogId);
        $batchUploadLog->status = $status;
        $batchUploadLog->job_completed_at = now();
        if (!empty($content['job_completed_log'])) {
            $batchUploadLog->job_completed_log = $content['job_completed_log'];
        }
        if (!empty($content['job_log_file'])) {
            $batchUploadLog->job_log_file = $content['job_log_file'];
        }

        return $batchUploadLog->save();
    }

    /**
     * 依 id 取得上傳資料
     *
     * @param $id
     *
     * @return mixed
     */
    public function getById($id): ?BatchUploadLog
    {
        return BatchUploadLog::find($id);
    }

    /**
     * 新增工作
     *
     * @param  [type] $batchUploadLogId
     * @return void
     */
    public function addJob($batchUploadLogId)
    {
        dispatch(new ProductImportJob($batchUploadLogId))->onQueue('backend-product');

        return true;
    }

    /**
     * 取得該檔案所在目錄

     * @param  $filePath
     * @return String
     */
    public function getFileFolderPath($filePath)
    {
        $result = '';
        $filePath = explode('/', $filePath);
        $lastKey = array_key_last($filePath);
        foreach ($filePath as $key => $val) {
            if ($key == $lastKey) {
                break;
            }
            $result .= $val . '/';
        }

        return $result;
    }

    /**
     *
     *
     * @return void
     */
    public function productForm($products)
    {
        try {
            $products = $products->groupBy('supplier_product_no');
            $supplier = Supplier::get();
            $brands = Brand::get();
            $tertiaryCategory = TertiaryCategory::get();
            $result = [];
            foreach ($products as $supplier_product_no => $product) {
                $productBasice = $product[0];
                $supplierData = $supplier->where('display_number', $productBasice['display_number'])->first();
                $brandsData = $brands->where('brand_name', $productBasice['brand_name'])->first();
                $tertiaryCategoryData = $tertiaryCategory->where('number', $productBasice['category_number'])->first();
                //抓第一筆為基本資料
                $productBasice = collect([
                    'stock_type' => $productBasice['stock_type'] ?? '',
                    'product_no' => Str::random(8),
                    'display_number' => $productBasice['display_number'] ?? '',
                    'product_name' => $productBasice['product_name'] ?? '',
                    'supplier_name' => $productBasice['supplier_name'] ?? '',
                    'brand_name' => $productBasice['brand_name'] ?? '',
                    'product_type' => $productBasice['product_type'] ?? '',
                    'spec_dimension' => $productBasice['spec_dimension'] ?? '',
                    'spec_1' => $productBasice['spec_1'] ?? '',
                    'spec_2' => $productBasice['spec_2'] ?? '',
                    'model' => $productBasice['model'] ?? '',
                    'length' => $productBasice['length'] ?? '',
                    'width' => $productBasice['width'] ?? '',
                    'height' => $productBasice['height'] ?? '',
                    'weight' => $productBasice['weight'] ?? '',
                    'list_price' => $productBasice['list_price'] ?? '',
                    'selling_price' => $productBasice['selling_price'] ?? '',
                    'purchase_price' => $productBasice['purchase_price'] ?? '',
                    'uom' => $productBasice['uom'] ?? '',
                    'product_brief_1' => $productBasice['product_brief_1'] ?? '',
                    'product_brief_2' => $productBasice['product_brief_2'] ?? '',
                    'product_brief_3' => $productBasice['product_brief_3'] ?? '',
                    'patent_no' => $productBasice['patent_no'] ?? '',
                    'is_with_warranty_text' => $productBasice['is_with_warranty_text'] ?? '',
                    'is_with_warranty' => $productBasice['is_with_warranty'] == 'Y' ? '1' : '0',
                    'warranty_days' => $productBasice['warranty_days'] ?? '0',
                    'warranty_scope' => $productBasice['warranty_scope'] ?? '',
                    'promotion_desc' => $productBasice['promotion_desc'] ?? '',
                    'promotion_start_at' => $productBasice['promotion_start_at'] ?? '',
                    'promotion_end_at' => $productBasice['promotion_end_at'] ?? '',
                    'has_expiry_date_text' => $productBasice['has_expiry_date'] ?? '',
                    'has_expiry_date' => $productBasice['has_expiry_date'] == 'Y' ? '1' : '0',
                    'expiry_days' => $productBasice['expiry_days'] ?? 0,
                    'expiry_receiving_days' => $productBasice['expiry_receiving_days'] ?? 0,
                    'description' => $productBasice['description'] ?? '',
                    'specification' => $productBasice['specification'] ?? '',
                    'rowNum' => $productBasice['rowNum'] ?? '',
                    'supplier_id' => $supplierData->id ?? '',
                    'tax_type' => 'TAXABLE',
                    'supplier_product_no' => $supplier_product_no,
                    'brand_id' => $brandsData->id ?? '',
                    'lgst_temperature' => $productBasice['lgst_temperature'] ?? 'N', //配送溫層
                    'storage_temperature' => $productBasice['storage_temperature'] ?? 'N', //存放溫層
                    'lgst_method' => 'HOME', //配送方式 : 宅配
                    'delivery_type' => 'IN_STOCK',
                    'display_number' => $productBasice['display_number'] ?? '',
                    'category_number' => $productBasice['category_number'] ?? '',
                    'category_id' => $tertiaryCategoryData->id ?? '',
                    'min_purchase_qty' => $productBasice['min_purchase_qty'] ?? '',
                    'web_category_hierarchy_ids' => $productBasice['web_category_hierarchy_ids'] ?? '',
                    'selling_channel' => $productBasice['selling_channel'] ?? '',
                    'payment_method'=>'TAPPAY_CREDITCARD,TAPPAY_LINEPAY,TAPPAY_JKOPAY',//付款方式給予預設值
                    'agent_id' => 1,
                    'created_by' => -1,
                    'updated_by' => -1,

                ]);
                $productItems = [];
                $itemPhotos = [] ;
                foreach ($product as $item) {
                    $spec_values = '';
                    $spec_values .= $item['spec_1_value'];
                    $spec_values .= $item['spec_2_value'];
                    array_push($productItems, collect([
                        'spec_values' => $spec_values,
                        'spec_1_value' => $item['spec_1_value'] ?? '',
                        'spec_2_value' => $item['spec_2_value'] ?? '',
                        'supplier_item_no' => $item['supplier_item_no'] ?? '',
                        'safty_qty' => $item['safty_qty'] ?? '',
                        'photo_name' => $item['photo_name'] ?? '',
                        'rowNum' => $item['rowNum'] ?? '',
                        'pos_item_no' => $item['pos_item_no'] ?? '',
                        'ean' => $item['ean'] ?? '',
                    ]));
                }

                if($item['photo_name'] !== ''){
                    array_push($itemPhotos , $item['photo_name']) ;
                }
                $result[$supplier_product_no] = collect([
                    'productBasice' => collect($productBasice),
                    'productItems' => collect($productItems),
                    'itemPhotos'  => collect($itemPhotos),
                ]);
            }

            return collect($result);

        } catch (\Exception $e) {
            Log::channel('batch_upload')->warning('整理product error' . $e->getMessage());

            return false;
        }
    }

    /**
     * 驗證product的內容是否符合規則
     *
     * @return array
     */
    public function verifyProduct($products,$productPhoto)
    {

        try {
            $result = [];
            $pos_item_no_arrays = []; //檢查輸入是否重複

            $supplier = $this->supplierService->getSuppliers();
            $brands = $this->brandsService->getBrands();
            $productItem = ProductItem::get();
            $getMaxLevelCategories = $this->webCategoryHierarchyService->getMaxLevelCategories();
            $getSupplierStockType = SupplierStockType::get();
            foreach ($products as $supplier_product_no => $product) {
                Log::channel('batch_upload')->warning("{$supplier_product_no}");

                $productBasice = $product['productBasice'];
                $errorMessage = [];
                $stockType = $productBasice['stock_type'] ?? ''; //庫存類型
                $productType = $productBasice['product_type'] ?? ''; //商品類型
                //「庫存類型」未填寫
                if (empty($productBasice['stock_type'])) {
                    $errorMessage[] = '「庫存類型」未填寫';
                }
                //「庫存類型」須為「B」或「T」
                if (!empty($productBasice['stock_type']) && !in_array($productBasice['stock_type'], ['A', 'B', 'T'])) {
                    $errorMessage[] = '「庫存類型」須為「A」或「B」或「T」';
                }
                //「供應商代碼」未填寫
                if (empty($productBasice['display_number'])) {
                    $errorMessage[] = '「供應商代碼」未填寫';
                }
                //「供應商代碼」錯誤
                $supplierIsset = $supplier->where('display_number', $productBasice['display_number'])->first();
                if (!empty($productBasice['display_number'])) {
                    if (!$supplierIsset) {
                        $errorMessage[] = '「供應商代碼」錯誤';
                    };
                }
                if(!empty($productBasice['stock_type']) && isset($supplierIsset->id) && $getSupplierStockType->where('supplier_id',$supplierIsset->id)->where('stock_type',$productBasice['stock_type'])->count() == 0 ){
                    $errorMessage[] = "「庫存類型」不允許指定{$productBasice['stock_type']}";
                }
                //「商品名稱」未填寫
                if (empty($productBasice['product_name'])) {
                    $errorMessage[] = '「商品名稱」未填寫';
                }
                //「商品名稱」不可超過100個字
                if (!empty($productBasice['product_name']) && mb_strlen($productBasice['product_name']) > 100) {
                    $errorMessage[] = '「商品名稱」不可超過100個字';
                }
                //「品牌」未填寫
                if (empty($productBasice['brand_name'])) {
                    $errorMessage[] = '「品牌」未填寫';
                }
                //「品牌」不存在
                if (!empty($productBasice['brand_name'])) {
                    $brandIsset = $brands->where('brand_name', $productBasice['brand_name'])->first();
                    if (!$brandIsset) {
                        $errorMessage[] = '「品牌」不存在';
                    }
                }
                //「廠商料號」未填寫
                if (empty($supplier_product_no)) {
                    $errorMessage[] = '「廠商料號」未填寫';
                }

                //「商品類型」未填寫
                if (empty($productBasice['product_type'])) {
                    $errorMessage[] = '「商品類型」未填寫';
                }
                // 「商品類型」須為「N」或「G」
                if (!empty($productBasice['product_type']) && !in_array($productBasice['product_type'], ['N', 'G'])) {
                    $errorMessage[] = '「商品類型」須為「N」或「G」';
                }
                // 「規格」須為「0」、「1」、或「2」
                if (!empty($productBasice['spec_dimension']) && !in_array($productBasice['spec_dimension'], ['0', '1', '2'])) {
                    $errorMessage[] = '「規格」須為「0」、「1」、或「2」';
                }
                switch ($productBasice['spec_dimension']) {
                    case '0':
                        //  單規格商品不可填寫「規格一(名稱)」、「規格二(名稱)」
                        if (!empty($productBasice['spec_1']) || !empty($productBasice['spec_2'])) {
                            $errorMessage[] = '單規格商品不可填寫「規格一(名稱)」、「規格二(名稱)」';
                        }
                        break;
                    case '1':
                        //一維多規格商品須填寫「規格一(名稱)」
                        if (empty($productBasice['spec_1'])) {
                            $errorMessage[] = '一維多規格商品須填寫「規格一(名稱)」';
                        }
                        //  一維多規格商品不可填寫「規格二(名稱)」
                        if (!empty($productBasice['spec_2'])) {
                            $errorMessage[] = '一維多規格商品不可填寫「規格二(名稱)」';
                        }
                        break;
                    case '2':
                        // 二維多規格商品須填寫「規格一(名稱)」、「規格二(名稱)」
                        if (empty($productBasice['spec_1']) || empty($productBasice['spec_2'])) {
                            $errorMessage[] = '二維多規格商品須填寫「規格一(名稱)」、「規格二(名稱)」';
                        }
                        break;
                    default:
                        # code...
                        break;
                }
                //「規格一(名稱)」最長只能有4個字
                if (!empty($productBasice['spec_1']) && mb_strlen($productBasice['spec_1']) > 20) {
                    $errorMessage[] = '「規格一(名稱)」最長只能有20個字';
                }
                // 「規格二(名稱)」最長只能有4個字
                if (!empty($productBasice['spec_2']) && mb_strlen($productBasice['spec_2']) > 20) {
                    $errorMessage[] = ' 「規格二(名稱)」最長只能有20個字';
                }

                // 「單位」未填寫
                if (empty($productBasice['uom'])) {
                    $errorMessage[] = '「單位」未填寫';
                }
                //「單位」不可超過10個字
                if (!empty($productBasice['uom']) && mb_strlen($productBasice['uom']) > 10) {
                    $errorMessage[] = '「單位」不可超過10個字';
                }
                //  「市價」未填寫
                if ((string)$productBasice['list_price'] == '' ) {
                    $errorMessage[] = ' 「市價」未填寫';
                }
                // 「售價」未填寫
                if ((string)$productBasice['selling_price'] == '') {
                    $errorMessage[] = '「售價」未填寫';
                }
                // 「成本」未填寫  「買斷」商品不須填寫
                if ((string)$productBasice['purchase_price'] == '' && in_array($productBasice['stock_type'], ['B', 'T'])) {
                    $errorMessage[] = '「成本」未填寫';
                }
                switch ($productType) {
                    case 'N': //一般品
                        //「成本」須小於「售價」，且成本與售價2欄為「正整數」(不可小於零)
                        if (!empty($productBasice['selling_price'])
                            && !empty($productBasice['purchase_price'])
                            && $productBasice['purchase_price'] >= $productBasice['selling_price']) {
                            $errorMessage[] = '「成本」須小於「售價」';
                        }
                        // 「售價」須為正整數
                        if (!empty($productBasice['selling_price']) && !preg_match("/^[1-9][0-9]*$/", $productBasice['selling_price'])) {
                            $errorMessage[] = '「售價」須為正整數';
                        }
                        // 「成本」須為正整數
                        if (!empty($productBasice['purchase_price']) && !preg_match("/^[1-9][0-9]*$/", $productBasice['purchase_price'])) {
                            $errorMessage[] = '「成本」須為正整數';
                        }
                        # code...
                        break;
                    case 'G': //贈品
                        //「成本」須為0、「售價」須為0
                        // 「售價」須為正整數
                        if (!empty($productBasice['selling_price']) && $productBasice['selling_price'] != 0) {
                            $errorMessage[] = '「成本」須為0';
                        }
                        // 「成本」須為正整數
                        if (!empty($productBasice['purchase_price']) && $productBasice['purchase_price'] != 0) {
                            $errorMessage[] = '「售價」須為0';
                        }
                        break;
                    case 'A': //加購品
                        // 「售價」須為正整數
                        if (!empty($productBasice['selling_price']) && !preg_match("/^[1-9][0-9]*$/", $productBasice['selling_price'])) {
                            $errorMessage[] = '「售價」須為正整數';
                        }
                        // 「成本」須為正整數
                        if (!empty($productBasice['purchase_price']) && !preg_match("/^[1-9][0-9]*$/", $productBasice['purchase_price'])) {
                            $errorMessage[] = '「成本」須為正整數';
                        }
                        break;
                    default:
                        # code...
                        break;
                }

                // 「市價」須為正整數
                if (!empty($productBasice['list_price']) && !preg_match("/^[1-9][0-9]*$/", $productBasice['list_price'])) {
                    $errorMessage[] = '「市價」須為正整數';
                }

                // 「商品簡述1」不可超過60個字
                if (!empty($productBasice['product_brief_1']) && mb_strlen($productBasice['product_brief_1']) > 60) {
                    $errorMessage[] = '「商品簡述1」不可超過60個字';
                }
                // 「商品簡述2」不可超過60個字
                if (!empty($productBasice['product_brief_2']) && mb_strlen($productBasice['product_brief_2']) > 60) {
                    $errorMessage[] = '「商品簡述2」不可超過60個字';
                }
                // 「商品簡述3」不可超過60個字
                if (!empty($productBasice['product_brief_3']) && mb_strlen($productBasice['product_brief_3']) > 60) {
                    $errorMessage[] = '「商品簡述3」不可超過60個字';
                }

                // 「保固範圍」不可超過250個字
                if (!empty($productBasice['warranty_scope']) && mb_strlen($productBasice['warranty_scope']) > 250) {
                    $errorMessage[] = '「保固範圍」不可超過250個字';
                }
                //「促銷小標-生效時間起」格式錯誤
                if ($productBasice['promotion_start_at'] === false) {
                    $errorMessage[] = '「促銷小標-生效時間起」格式錯誤';
                }
                //如果有輸入生效時間起，促銷小標-生效時間訖則為必填
                if (!empty($productBasice['promotion_start_at']) && empty($productBasice['promotion_end_at'])) {
                    $errorMessage[] = '如果有輸入「促銷小標-生效時間起」，「促銷小標-生效時間訖」為必填';
                }
                //「促銷小標-生效時間訖」格式錯誤
                if ($productBasice['promotion_start_at'] === false) {
                    $errorMessage[] = ' 「促銷小標-生效時間訖」格式錯誤';
                }

                //「促銷小標-生效時間起」不能大於「促銷小標-生效時間訖」
                if (!empty($productBasice['promotion_start_at']) && !empty($productBasice['promotion_end_at'])) {
                    if ($productBasice['promotion_start_at']->gt($productBasice['promotion_end_at'])) {
                        $errorMessage[] = '「促銷小標-生效時間起」不能大於「促銷小標-生效時間訖」';
                    }
                }
                //「效期控管」未填寫
                if (empty($productBasice['has_expiry_date_text'])) {
                    $errorMessage[] = '「效期控管」未填寫';
                }
                // 「有無保固」須為「Y」或「N」
                if (!empty($productBasice['has_expiry_date_text']) && !in_array($productBasice['has_expiry_date_text'], ['Y', 'N'])) {
                    $errorMessage[] = '「效期控管」須為「Y」或「N」';
                }
                // 「效期控管天數」須為正整數
                if ($productBasice['has_expiry_date_text'] == 'Y' && !preg_match("/^[1-9][0-9]*$/", $productBasice['expiry_days'])) {
                    $errorMessage[] = '「效期控管天數」須為正整數';
                }
                // 「效期控管」為「Y」時「效期控管天數」為必填
                if ($productBasice['has_expiry_date_text'] == 'Y' && empty($productBasice['expiry_days'])) {
                    $errorMessage[] = '「效期控管」為「Y」時「效期控管天數」為必填';
                }
                // 「允收期(天)」須為正整數
                if ($productBasice['has_expiry_date_text'] == 'Y' && !preg_match("/^[1-9][0-9]*$/", $productBasice['expiry_receiving_days'])) {
                    $errorMessage[] = '「效期控管天數」須為正整數';
                }
                // 「效期控管」為「Y」時「允收期(天)」為必填
                if ($productBasice['has_expiry_date_text'] == 'Y' && empty($productBasice['expiry_receiving_days'])) {
                    $errorMessage[] = '「效期控管」為「Y」時「允收期(天)」為必填';
                }
                //「商品內容」未填寫
                if (empty($productBasice['description'])) {
                    $errorMessage[] = '「商品內容」未填寫';
                }
                //「商品規格」未填寫
                if (empty($productBasice['specification'])) {
                    $errorMessage[] = '「商品規格」未填寫';
                }
                //  指定「POS小分類」代碼
                if (empty($productBasice['category_number'])) {
                    $errorMessage[] = '「POS小分類」代碼未填寫';
                }
                //「POS小分類」代碼不存在
                if (empty($productBasice['category_id'])) {
                    $errorMessage[] = '「POS小分類」代碼不存在';
                }
                //至少填寫一組分類ID，若有多組，請以逗號分隔，例如：36
                if (empty($productBasice['web_category_hierarchy_ids'])) {
                    $errorMessage[] = '至少填寫一組分類ID，若有多組，請以逗號分隔，例如：36,52';
                }
                $web_category_hierarchy_id_array = explode(",", $productBasice['web_category_hierarchy_ids']);
                foreach ($web_category_hierarchy_id_array as $web_category_hierarchy_id) {
                    if (!$getMaxLevelCategories->where('id', $web_category_hierarchy_id)->first()) {
                        $errorMessage[] = "找不到ID:{$web_category_hierarchy_id}的前台分類";
                    }
                    if ($getMaxLevelCategories->where('content_type', 'M')->where('id', $web_category_hierarchy_id)->first()) {
                        $errorMessage[] = "前台分類 ID :{$web_category_hierarchy_id}不可為指定賣場";
                    }
                }
                if (in_array($stockType, ['A', 'B'])) {
                    //  「最小入庫量」未填寫
                    if (empty($productBasice['min_purchase_qty'])) {
                        $errorMessage[] = '「最小入庫量」未填寫';
                    }
                    // 「材積_長」未填寫
                    if (empty($productBasice['length'])) {
                        $errorMessage[] = '「材積_長」未填寫';
                    }
                    //「材積_寬」未填寫
                    if (empty($productBasice['width'])) {
                        $errorMessage[] = '「材積_寬」未填寫';
                    }
                    // 「材積_高」未填寫
                    if (empty($productBasice['height'])) {
                        $errorMessage[] = '「材積_高」未填寫';
                    }
                    //  「重量」未填寫
                    if (empty($productBasice['weight'])) {
                        $errorMessage[] = '「重量」未填寫';
                    }
                    //  「重量」須為正整數
                    if (!empty($productBasice['weight']) && !preg_match("/^[1-9][0-9]*$/", $productBasice['weight'])) {
                        $errorMessage[] = '「重量」須為正整數';
                    }

                }
                //  「最小入庫量」須為正整數
                if (!empty($productBasice['min_purchase_qty']) && !preg_match("/^[1-9][0-9]*$/", $productBasice['min_purchase_qty'])) {
                    $errorMessage[] = '「最小入庫量」須為正整數';
                }
                //「材積_長」須為正整數
                if (!empty($productBasice['length']) && !preg_match("/^[1-9][0-9]*$/", $productBasice['length'])) {
                    $errorMessage[] = '「材積_長」須為正整數';
                }
                //「材積_寬」須為正整數
                if (!empty($productBasice['width']) && !preg_match("/^[1-9][0-9]*$/", $productBasice['width'])) {
                    $errorMessage[] = '「材積_寬」須為正整數';
                }
                // 「材積_高」須為正整數
                if (!empty($productBasice['height']) && !preg_match("/^[1-9][0-9]*$/", $productBasice['height'])) {
                    $errorMessage[] = '「材積_高」須為正整數';
                }
                // 「保固天數」須為正整數
                if (!empty($productBasice['is_with_warranty_text']) && $productBasice['is_with_warranty_text'] == 'Y' && empty($productBasice['warranty_days'])) {
                    $errorMessage[] = '「有無保固」填寫Y時 需填寫「保固天數」';
                }
                // 「保固天數」須為正整數
                if (!empty($productBasice['is_with_warranty_text']) == 'Y' && !preg_match("/^[1-9][0-9]*$/", $productBasice['warranty_days'])) {
                    $errorMessage[] = '「保固天數」須為正整數';
                }
                // 「有無保固」須為「Y」或「N」
                if (!empty($productBasice['is_with_warranty_text']) && !in_array($productBasice['is_with_warranty_text'], ['Y', 'N'])) {
                    $errorMessage[] = '「有無保固」須為「Y」或「N」';
                }

                //「商品通路」未填寫
                if (empty($productBasice['selling_channel'])) {
                    $errorMessage[] = '「商品通路」未填寫';
                }
                // 「商品通路」須為「E」或「S」或「W」
                if (!empty($productBasice['selling_channel']) && !in_array($productBasice['selling_channel'], ['E', 'S', 'W'])) {
                    $errorMessage[] = '「商品通路」須為「E」或「S」或「W」';
                }

                // 「配送溫層」須為「N」或「C」
                if (!empty($productBasice['lgst_temperature']) && !in_array($productBasice['lgst_temperature'], ['N', 'C'])) {
                    $errorMessage[] = '「配送溫層」須為「N」或「C」';
                }
                switch($productBasice['lgst_temperature']){
                    case 'C':
                        $productBasice['lgst_temperature'] = 'CHILLED';
                        break;
                    default:
                        $productBasice['lgst_temperature'] = 'NORMAL';
                        break;
                }

                // 「存放溫層」須為「N」或「A」或「C」
                if (!empty($productBasice['storage_temperature']) && !in_array($productBasice['storage_temperature'], ['N', 'A', 'C'])) {
                    $errorMessage[] = '「存放溫層」須為「N」或「A」或「C」';
                }
                switch($productBasice['storage_temperature']){
                    case 'A':
                        $productBasice['storage_temperature'] = 'AIR';
                        break;
                    case 'C':
                        $productBasice['storage_temperature'] = 'CHILLED';
                        break;
                    default:
                        $productBasice['storage_temperature'] = 'NORMAL';
                        break;
                }
                
                foreach ($product['productItems'] as $item) {
                    if (in_array($stockType, ['A', 'B'])) {
                        // 「安全庫存量」未填寫
                        if (empty($item['safty_qty'])) {
                            $errorMessage[] = '「安全庫存量」未填寫';
                        }
                    }
                    // 「安全庫存量」須為正整數
                    if (!empty($item['safty_qty']) && !preg_match("/^[1-9][0-9]*$/", $item['safty_qty'])) {
                        $errorMessage[] = '「安全庫存量」須為正整數';
                    }
                    //「廠商貨號」不可超過100個字
                    if (!empty($item['supplier_item_no']) && mb_strlen($item['supplier_item_no']) > 100) {
                        $errorMessage[] = '「廠商貨號」不可超過100個字';
                    }
                    //「國際條碼」不可超過30個字
                    if (!empty($item['ean']) && mb_strlen($item['ean']) > 30) {
                        $errorMessage[] = '「國際條碼」不可超過30個字';
                    }
                    // 「買斷」、「寄售」商品需填寫POS品號 // 商城後台才有的欄位
                    if (empty($item['pos_item_no']) && in_array($productBasice['stock_type'], ['A', 'B'])) {
                        $errorMessage[] = '「買斷」、「寄售」商品需填寫POS品號';
                    }
                    // POS商品編號已跟現有品項重複
                    if (!empty($item['pos_item_no'])) {
                        if ($productItem->where('pos_item_no', $item['pos_item_no'])->count() > 0) {
                            $errorMessage[] = 'POS商品編號已跟現有品項重複';
                        };
                    }
                    // 匯入的POS品號有重複
                    if (!empty($item['pos_item_no'])) {
                        if (in_array($item['pos_item_no'], $pos_item_no_arrays)) {
                            $errorMessage[] = "匯入的POS品號有重複{$item['pos_item_no']}";
                        } else {
                            array_push($pos_item_no_arrays, $item['pos_item_no']);
                        }
                    }
                    foreach($productPhoto as $photo){
                        //有指定照片再去檢查
                        if($photo['supplier_product_no'] == $supplier_product_no && !empty($item['photo_name']) && !in_array($item['photo_name'],$photo['photos'])){ 
                            $errorMessage[] = '「Item圖示」不存在';
                        }
                    }
                }
                if (!empty($errorMessage)) {
                    array_push($result, [
                        'rowNum' => $productBasice['rowNum'],
                        'supplierProductNo' => $supplier_product_no,
                        'errorMessage' => implode(' + ', $errorMessage),
                    ]);
                }
            }

            return $result;

        } catch (\Exception $e) {
            array_push($result, [
                'rowNum' => '',
                'supplierProductNo' => '',
                'errorMessage' => '驗證產品時發生未預期的錯誤',
            ]);

            Log::channel('batch_upload')->warning("驗證產品時發生未預期的錯誤");
            Log::channel('batch_upload')->error($e->getMessage());

            return $result;

        }

    }

    public function verifySkuItem($products)
    {
        $result = [];
        try {
            foreach ($products as $supplier_product_no => $product) {
                $spec_1_values = collect([]);
                $spec_2_values = collect([]);
                $productBasice = $product['productBasice'];
                $errorMessage = [];

                foreach ($product['productItems'] as $item) {
                    if ($item['spec_1_value'] !== '') {
                        $spec_1_values->push($item['spec_1_value']);
                    }
                    if ($item['spec_2_value'] !== '') {
                        $spec_2_values->push($item['spec_2_value']);
                    };
                }

                // 驗證匯入規格是否符合規則
                $spec_1_count = $spec_1_values->unique()->count() == 0 ? 1 : $spec_1_values->unique()->count();
                $spec_2_count = $spec_2_values->unique()->count() == 0 ? 1 : $spec_2_values->unique()->count();
                $itemCorrectNum = $spec_1_count * $spec_2_count;
                $itemRealNum = $product['productItems']->count();

                if ($itemRealNum !== $itemCorrectNum) {
                    $errorMessage[] = "需要{$itemRealNum}筆品項，但實際匯入規格只有{$itemCorrectNum}筆品項";
                }
                switch ($productBasice['spec_dimension']) {
                    case '0':
                        if ($itemRealNum > 1) {
                            $errorMessage[] = "  單規格商品：同個檔案內，不能有多筆「廠商料號」相同的資料";
                        }
                        break;
                    case '1':
                        if ($spec_1_values->count() !== $spec_1_values->unique()->count()) {
                            $errorMessage[] = "一維多規格商品：同個檔案內，不能有多筆「廠商料號」、「規格一」相同的資料";
                        }
                        break;
                    case '2':
                        $cartesian = $this->cartesian([
                            $spec_1_values->unique(),
                            $spec_2_values->unique(),
                        ]);
                        foreach ($cartesian as $spec) {
                            $find = $product['productItems']->where('spec_1_value', $spec[0])->where('spec_2_value', $spec[1])->all();
                            if (count($find) > 1) {
                                $errorMessage[] = "二維多規格商品：同個檔案內，不能有多筆「廠商料號」、「規格一」、「規格二」相同的資料";
                            }
                        }
                        break;
                    default:
                        break;
                }
                if (!empty($errorMessage)) {
                    array_push($result, [
                        'rowNum' => $productBasice['rowNum'],
                        'supplierProductNo' => $supplier_product_no,
                        'errorMessage' => implode(' + ', $errorMessage),
                    ]);
                }
            }
        } catch (\Exception $e) {
            array_push($result, [
                'rowNum' => '',
                'supplierProductNo' => '',
                'errorMessage' => "驗證品項時發生未預期的錯誤：{$e->getMessage()}",
            ]);
            Log::channel('batch_upload')->warning("驗證品項時發生未預期的錯誤");
            Log::channel('batch_upload')->error($e);
        }

        return $result;
    }

    /**
     * 迪卡爾積演算法
     *
     * @param  array $input
     * @return array
     */
    public function cartesian(array $input)
    {
        $result = [[]];
        foreach ($input as $key => $values) {
            $append = [];
            foreach ($values as $value) {
                foreach ($result as $data) {
                    $append[] = $data + [$key => $value];
                }
            }
            $result = $append;
        }

        return $result;
    }

    /**
     * 檢查照片是否符合excel 設定以及要求規格
     *
     *  @return Array
     */
    public function verifyPhoto($endPath, $productPhoto)
    {
        $result = [];
        foreach ($productPhoto as $val) {
            //取得目錄底下的檔案
            $folder = $endPath . $val['supplier_product_no'];
            $files = Storage::disk('s3')->files($folder);
            $fileBasename = collect($files)->map(function ($obj) {
                return $obj = basename($obj);
            });
            $photos = collect($val['photos']);
            if ($photos->count() == 0) {
                array_push($result, [
                    'supplierProductNo' => $val['supplier_product_no'],
                    'imageName' => '',
                    'errorMessage' => '產品至少要一張照片',
                ]);
            }
            //step 1 先交叉比對 excel 設定的png是否有符合上傳的zip 檔案
            $diff = $photos->diff($fileBasename);
            if ($diff->count() > 0) {
                foreach ($diff as $diffVal) {
                    array_push($result, [
                        'supplierProductNo' => $val['supplier_product_no'],
                        'imageName' => $diffVal,
                        'errorMessage' => '匯入zip檔案找不到該照片',
                    ]);
                }
            }
            //step 2 檢查照片的比例大小
            foreach ($photos as $photo) {
                $imagePath = "{$folder}/{$photo}";
                $image = Storage::disk('s3')->exists("{$folder}/{$photo}");
                if ($image) {
                    $image = Image::make(Storage::disk('s3')->get($imagePath));
                    if ($image->width() - $image->height() !== 0) {
                        array_push($result, [
                            'supplierProductNo' => $val['supplier_product_no'],
                            'imageName' => $photo,
                            'errorMessage' => '照片的比例必須要1:1',
                        ]);
                    }
                    if ($image->width() < 480 || $image->height() < 480) {
                        array_push($result, [
                            'supplierProductNo' => $val['supplier_product_no'],
                            'imageName' => $photo,
                            'errorMessage' => '寬*高至少須為480*480',
                        ]);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * 新增商品 照片
     *
     * @param  Collection $products 商品
     * @param  string $endPath 路徑
     * @param  Collection $productPhoto 上傳照片資訊
     * @return void
     */
    public function addProductForm($products, $endPath, $productPhoto)
    {
        try {
            DB::beginTransaction();
            $result = [];
            foreach ($products as $supplier_product_no => $product) {
                //只取product table需要的欄位
                $productBasice = $product['productBasice'];
                $webCategoryHierarchyIds = $productBasice['web_category_hierarchy_ids'];
                $productBasice = $productBasice->except(['display_number',
                    'supplier_name',
                    'brand_name',
                    'rowNum',
                    'has_expiry_date_text',
                    'is_with_warranty_text',
                    'category_number',
                    'web_category_hierarchy_ids',
                ]);
                if (empty($productBasice['promotion_start_at']) || empty($productBasice['promotion_end_at'])) {
                    $productBasice = $productBasice->except(['promotion_start_at', 'promotion_end_at']);
                }
                switch ($productBasice['selling_channel']) {
                    case 'E':
                        $productBasice['selling_channel'] = 'EC';
                        break;
                    case 'S':
                        $productBasice['selling_channel'] = 'STORE';
                        break;
                    case 'W':
                        $productBasice['selling_channel'] = 'WHOLE';
                        break;
                    default:
                        # code...
                        break;
                }
                // 「最小入庫量」未填寫 int 
                if($productBasice['min_purchase_qty'] == ''){
                    $productBasice['min_purchase_qty'] = 0;
                }
                if($productBasice['length'] == ''){
                    $productBasice['length'] = 0;
                }
                if($productBasice['width'] == ''){
                    $productBasice['width'] = 0;
                }
                if($productBasice['height'] == ''){
                    $productBasice['height'] = 0;
                }
                if($productBasice['weight'] == ''){
                    $productBasice['weight'] = 0;
                }
                if($productBasice['warranty_days'] == ''){
                    $productBasice['warranty_days'] = 0;
                }



                //寫入新品提報table
                $productId = Product::create($productBasice->toArray())->id;
                $product_no = $this->universalService->getDocNumber('products', ['stock_type' => $productBasice['stock_type'], 'id' => $productId]);
                Product::where('id', $productId)->update(['product_no' => $product_no]);
                //前台分類
                $web_category_hierarchy_id_array = explode(",", $webCategoryHierarchyIds);
                foreach ($web_category_hierarchy_id_array as $key => $val) {
                    WebCategoryProduct::create([
                        'web_category_hierarchy_id' => $val,
                        'product_id' => $productId,
                        'sort' => $key,
                        'created_by' => -1,
                        'updated_by' => -1,
                    ]);
                }
                //照片裁切並且重新命名
                $itemPhotos = $productPhoto->where('supplier_product_no', $supplier_product_no)->first();
                $newItemPhotos = collect([]);
                foreach ($itemPhotos['photos'] as $photo) {
                    $imagePath = "{$endPath}{$supplier_product_no}/{$photo}";
                    $imageObj = Storage::disk('s3')->exists($imagePath); //檢查檔案是否存在
                    if ($imageObj) {
                        $photoType = '.' . explode('.', $photo)[1];
                        $newPhotoName = '480X480_' . uniqid(date('YmdHis')) . Str::random(4);
                        $image = Storage::disk('s3')->get($imagePath);
                        $img = Image::make($image);
                        $img->resize('480', '480');
                        $resource = $img->stream()->detach();
                        $newPath = "products/{$productId}/{$newPhotoName}{$photoType}";
                        Storage::disk('s3')->put("{$newPath}", $resource, 'public');
                        $newItemPhotos->push([
                            'oldImagePath' => $imagePath,
                            'newImagePath' => $newPath,
                            'name' => $photo,
                        ]);
                    }
                }
                //將新照片新增到SupReqProductPhoto
                foreach ($newItemPhotos as $key => $newItemPhoto) {
                    $insertImg = [
                        'product_id' => $productId,
                        'photo_name' => $newItemPhoto['newImagePath'],
                        'sort' => $key,
                        'created_by' => -1,
                        'updated_by' => -1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    DB::table('product_photos')->insert($insertImg);
                }
                //google 圖檔新增
                if (!empty($itemPhotos['google_shop_photo_name'])) {
                    $shopPhotoName = $newItemPhotos->where('name', $itemPhotos['google_shop_photo_name'])->first();
                    $shopPhotoName = $shopPhotoName['newImagePath'] ?? '';
                    if ($shopPhotoName !== '') {
                        Product::where('id', $productId)->update(['google_shop_photo_name' => $shopPhotoName]);
                    }
                }
                $produceSkuForm = $this->produceSkuForm($product['productItems']); //產出類似前端SKU的JSON格式
                $add_item_no = 1;
                foreach ($produceSkuForm['skuListdata'] as $key => $item) {
                    $photoName = $newItemPhotos->where('name', $item['photo_name'])->first();
                    $photoName = $photoName['newImagePath'] ?? '';
                    $skuInsert = [
                        'agent_id' => 1,
                        'product_id' => $productId,
                        'sort' => $item['sort'] ?? 0,
                        'spec_1_value' => $item['spec_1_value'] ?? '',
                        'spec_2_value' => $item['spec_2_value'] ?? '',
                        'pos_item_no' => $item['pos_item_no'] ?? '',
                        'supplier_item_no' => $item['supplier_item_no'],
                        'item_no' => $product_no . str_pad($add_item_no, 4, "0", STR_PAD_LEFT), //新增時直接用key生成id
                        'photo_name' => $photoName,
                        'safty_qty' => $item['safty_qty'] == '' ? 0 : $item['safty_qty'] ,
                        'is_additional_purchase' => $item['is_additional_purchase'],
                        'status' => $item['status'],
                        'created_by' => -1,
                        'updated_by' => -1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                    $produceSkuForm['skuListdata'][$key]['id'] = ProductItem::create($skuInsert)->id;
                    $produceSkuForm['skuListdata'][$key]['item_no'] = $product_no . str_pad($add_item_no, 4, "0", STR_PAD_LEFT);
                    $produceSkuForm['skuListdata'][$key]['safty_qty'] = $item['safty_qty'] == '' ? 0 : $item['safty_qty'];
                    $add_item_no += 1;
                }

                DB::table('product_spec_info')->insert([
                    'product_id' => $productId,
                    'spec_value_list' => json_encode($produceSkuForm['specList']),
                    'item_list' => json_encode($produceSkuForm['skuListdata']),
                    'created_by' => -1,
                    'updated_by' => -1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            $result['count'] = $products->count();
            Storage::disk('s3')->deleteDirectory($endPath);
            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('batch_upload')->warning($e->getMessage());

            return false;
        }
    }

    /**
     * 產出前端SKU格式
     *
     * @param  [array] $productItems
     * @param  [int] $supReqProductsId
     * @return void
     */
    public function produceSkuForm($productItems)
    {
        //取出不重複的規格
        $spec_1_values = collect([]);
        $spec_2_values = collect([]);
        foreach ($productItems as $key => $item) {
            $spec_1_values->push($item['spec_1_value']);
            $spec_2_values->push($item['spec_2_value']);
        }
        $spec_1_values = $spec_1_values->unique();
        $spec_2_values = $spec_2_values->unique();
        //規格List
        $specList = [];
        $specList['spec_1'] = [];
        $specList['spec_2'] = [];
        $sortIndex = 0;
        foreach ($spec_1_values as $spec_1) {
            $specList['spec_1'][] = [
                "name" => $spec_1,
                "sort" => $sortIndex,
                "only_key" => Str::random(4),
            ];
            $sortIndex += 1;
        }
        $sortIndex = 0;
        foreach ($spec_2_values as $spec_2) {
            $specList['spec_2'][] = [
                "name" => $spec_2,
                "sort" => $sortIndex,
                "only_key" => Str::random(4),
            ];
            $sortIndex += 1;
        }
        $skuListdata = [];
        $specOne = collect($specList['spec_1']);
        $specTwo = collect($specList['spec_2']);
        foreach ($productItems as $key => $item) {
            $findSpecOne = $specOne->where('name', $item['spec_1_value'])->first();
            $findSpecTwo = $specTwo->where('name', $item['spec_2_value'])->first();
            $sortKey = '';
            if ($findSpecOne) {
                $sortKey = $findSpecOne['sort'];
            }
            if ($findSpecTwo) {
                $sortKey .= $findSpecTwo['sort'];
            }
            $skuListdata[] = [
                "id" => "",
                "sort_key" => $sortKey,
                "sort" => $key,
                "spec_1_value" => $item["spec_1_value"],
                "spec_2_value" => $item["spec_2_value"],
                "spec_1_only_key" => $findSpecOne["only_key"] ?? "",
                "spec_2_only_key" => $findSpecTwo["only_key"] ?? "",
                "item_no" => "",
                "pos_item_no" => $item["pos_item_no"],
                "supplier_item_no" => $item["supplier_item_no"],
                "safty_qty" => $item['safty_qty'] == '' ? 0 : $item['safty_qty'],
                "ean" => $item["ean"],
                "is_additional_purchase" => "1", //是否追加先寫死 1
                "status" => "1", //狀態寫死 1
                "photo_name" => $item["photo_name"],
            ];
        }

        return [
            'skuListdata' => $skuListdata,
            'specList' => $specList,
        ];
    }

    /**
     * 直接整理好輸出exprot error log 格式匯出
     */
    public function exportForm($verifys)
    {
        $result = collect();
        foreach ($verifys as $verifyType => $content) {
            if (count($content) > 0) {
                switch ($verifyType) {
                    case 'verifyProduct':
                        $result->push([
                            '異常items',
                        ]);
                        $result->push([
                            '行數',
                            '廠商料號',
                            '錯誤訊息',
                        ]);
                        break;
                    case 'verifySkuItem':
                        $result->push([
                            '異常規格',
                        ]);
                        $result->push([
                            '行數',
                            '廠商料號',
                            '錯誤訊息',
                        ]);
                        break;
                    case 'verifyPhoto':
                        $result->push([
                            '異常photos',
                        ]);
                        $result->push([
                            '廠商料號',
                            '照片名稱',
                            '錯誤訊息',
                        ]);
                        break;
                    default:
                        # code...
                        break;
                }
            }

            foreach ($content as $itemError) {
                $result->push($itemError);
            }
            $result->push([]);
        }

        return $result;
    }

    public function batchLogList()
    {
        $batchUploadLog = BatchUploadLog::with("supplier")->orderBy("id", 'desc')->whereNull('supplier_id');

        return $batchUploadLog->get();
    }
}

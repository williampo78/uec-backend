<?php

namespace App\Services;

use App\Enums\BatchUploadLogStatus;
use App\Jobs\ProductImportJob;
use App\Models\BatchUploadLog;
use App\Models\Brand;
use App\Models\Supplier;
use App\Models\TertiaryCategory;
use App\Services\WebCategoryHierarchyService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductBatchService
{

    private $webCategoryHierarchyService ;

    public function __construct(WebCategoryHierarchyService $webCategoryHierarchyService)
    {
        $this->webCategoryHierarchyService = $webCategoryHierarchyService ;
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
        $filePath = "uploads/batch/{$dateY}/{$random}/{$fileName}";

        Storage::put(
            $filePath,
            file_get_contents($file->getRealPath())
        );

        return [
            'originalName' => $originalName,
            'filePath' => $filePath,
        ];
    }

    public function addBatchUploadLog($inputLogData)
    {
        // try {
            $result = BatchUploadLog::create($inputLogData);
            return $result->id;
        // } catch (\Exception $e) {
        //     Log::channel('batch_upload')->warning('addBatchUploadLog' . $e->getMessage());
        //     return false;
        // }
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
    public function addJob($batchUploadLogId){
        dispatch(new ProductImportJob($batchUploadLogId))->onQueue('common');
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
    public function productForm($products){
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
                $tertiaryCategoryData = $tertiaryCategory->where('number',$productBasice['category_number'])->first();
                //抓第一筆為基本資料
                $productBasice = collect([
                    'stock_type' => $productBasice['stock_type'] ?? '',
                    'request_no' => Str::random(8),
                    'display_number' => $productBasice['display_number'] ?? '',
                    'product_name' => $productBasice['product_name'] ?? '',
                    'supplier_name' => $productBasice['supplier_name'] ?? '',
                    'brand_name' => $productBasice['brand_name'] ?? '',
                    'product_type' => $productBasice['product_type'] ?? '',
                    'spec_dimension' => $productBasice['spec_dimension'] ?? '',
                    'spec_1' => $productBasice['spec_1'] ?? '',
                    'spec_2' => $productBasice['spec_2'] ?? '',
                    'min_purchase_qty' => $productBasice['min_purchase_qty'] ?? '',
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
                    'expiry_days' => $productBasice['expiry_days'] ?? '',
                    'expiry_receiving_days' => $productBasice['expiry_receiving_days'] ?? '',
                    'description' => $productBasice['description'] ?? '',
                    'specification' => $productBasice['specification'] ?? '',
                    'rowNum' => $productBasice['rowNum'] ?? '',
                    'supplier_id' => $supplierData->id ?? '',
                    'tax_type' => $supplierData->tax_type ?? '',
                    'supplier_product_no' => $supplier_product_no,
                    'brand_id' => $brandsData->id ?? '',
                    'lgst_temperature' => 'NORMAL', //常溫
                    'lgst_method' => 'HOME', //配送方式 : 宅配
                    'delivery_type' => 'IN_STOCK',
                    'display_number' => $productBasice['display_number'] ?? '',
                    'category_number'=> $productBasice['category_number'] ?? '' ,
                    'category_id'=>$tertiaryCategoryData->id ?? '',
                    'web_category_hierarchy_ids'=>$productBasice['web_category_hierarchy_ids'] ?? '' ,
                    'next_stage' => 1,
                    'created_by' => -1,
                    'updated_by' => -1,
                ]);
                $productItems = [];
                foreach ($product as $item) {
                    $spec_values = '';
                    $spec_values .= $item['spec_1_value'];
                    $spec_values .= $item['spec_2_value'];
                    array_push($productItems, collect([
                        'spec_values' => $spec_values,
                        'spec_1_value' => $item['spec_1_value'] ?? '',
                        'spec_2_value' => $item['spec_2_value'] ?? '',
                        'supplier_item_no' => $item['supplier_item_no'] ?? '',
                        'prepared_qty' => $item['prepared_qty'] ?? '',
                        'safty_qty' => $item['safty_qty'] ?? '',
                        'photo_name' => $item['photo_name'] ?? '',
                        'rowNum' => $item['rowNum'] ?? '',
                        'pos_item_no'=>$productBasice['pos_item_no'] ?? '',

                    ]));
                }

                $result[$supplier_product_no] = collect([
                    'productBasice' => collect($productBasice),
                    'productItems' => collect($productItems),
                ]);

            }

            return collect($result);

        } catch (\Exception $e) {
            Log::channel('batch_upload')->warning('整理product error' . $e->getMessage());

            return false;
        }
    }
}

<?php

namespace App\Jobs;

use App\Enums\BatchUploadLogStatus;
use App\Exports\Product\ErrorImpoerLogExport;
use App\Imports\Product\BatchImport;
use App\Services\ProductBatchService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ProductImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $signature = '[Job]BasicProductImportJob';

    protected $logId;

    /**
     * The number of times the job may be attempted.
     * If you do not specify a value for the --tries option,
     * jobs will be attempted indefinitely:
     * @var int
     */
    public $tries = 1;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 1200;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->logId = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ProductBatchService $productBatchService)
    {
        try {
            Log::channel('batch_upload')->warning("log id : {$this->logId} 開始寫入");
            try {
                $productBatchData = $productBatchService->getById($this->logId);
                $excelData = Excel::toArray(new BatchImport, $productBatchData->saved_file_1_name, '');
                $products = collect($excelData[0]); // 取得商品
                $productPhoto = collect($excelData[1]); //取得照片
            } catch (\Exception $e) {
                Log::channel('batch_upload')->warning($e->getMessage());
                $productBatchService->updateStatusById($productBatchData->id, 2, [
                    'job_completed_log' => '取得Excel內容失敗',
                ]);
                Log::channel('batch_upload')->warning("log id : {$this->logId} 取得Excel內容失敗");

                return false;
            }
            $zipAbsolutePath = Storage::path($productBatchData->saved_file_2_name);
            $endPath = $productBatchService->getFileFolderPath($productBatchData->saved_file_2_name);
            $extract = Storage::disk('s3')->extractTo($endPath, $zipAbsolutePath); //壓縮完後丟到S3
            if (!$extract) {
                $productBatchService->updateStatusById($productBatchData->id, 2, [
                    'job_completed_log' => '解壓縮失敗',
                ]);

                return false;
            }
            $products = $productBatchService->productForm($products);
            $verifyProduct = $productBatchService->verifyProduct($products); //檢查基本商品
            $verifySkuItem = $productBatchService->verifySkuItem($products); //進階檢查規格
            $verifyPhoto = $productBatchService->verifyPhoto($endPath, $productPhoto ,$products); //檢查照片
            Storage::deleteDirectory($endPath);

            // //驗證未過
            if (!empty($verifyProduct) || !empty($verifySkuItem || !empty($verifyPhoto))) {

                $random = Str::random(40);
                $excelEndPath = config('uec.batch_upload_path')."{$random}.xlsx";

                Excel::store(new ErrorImpoerLogExport($productBatchService->exportForm([
                    'verifyProduct' => $verifyProduct,
                    'verifySkuItem' => $verifySkuItem,
                    'verifyPhoto' => $verifyPhoto,
                ])), $excelEndPath, 's3');

                $productBatchService->updateStatusById($productBatchData->id, 2, [
                    'job_completed_log' => '無商品產生，請檢查並更正資料後，再重新上傳！',
                    'job_log_file' => $excelEndPath,
                ]);

                return false;
            }

            // 如果都沒錯誤 準備寫入新品提報table
            $products = $productBatchService->addProductForm($products, $endPath, $productPhoto);

            if ($products) {
                $job_completed_log = "產生《{$products['count']}》個產品";
                $productBatchService->updateStatusById($productBatchData->id, 1, [
                    'job_completed_log' => $job_completed_log,
                ]);
                Log::channel('batch_upload')->warning("log id : {$this->logId} 寫入完成");

                return true;
            } else {
                $productBatchService->updateStatusById($productBatchData->id, 2, [
                    'job_completed_log' => '建立產品時發生未預期的錯誤',
                ]);

                return false;
            }

        } catch (Exception $e) {
            $productBatchService->updateStatusById($this->logId, BatchUploadLogStatus::STATUS_FAILED,[
                'job_completed_log' => "程式異常：{$e->getMessage()}",
            ]);
            Log::channel('batch_upload')->warning("catch log id : {$this->logId} 失敗" . $e->getMessage());
        }
    }

    /***
     * 失敗後回寫狀態及增加Log 處理
     *
     * @param Exception $e 任何錯誤
     *
     * @return void
     */
    public function failed($e)
    {
        try {
            Log::channel('batch_upload')->warning("failed try log id : {$this->logId} 失敗" . $e->getMessage());
        } catch (Exception $e) {
            Log::channel('batch_upload')->warning("failed catch log id : {$this->logId} 失敗" . $e->getMessage());
        }
    }
}

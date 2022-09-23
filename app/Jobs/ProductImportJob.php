<?php

namespace App\Jobs;

use App\Enums\BatchUploadLogStatus;
use App\Services\ProductBatchService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
        $this->batchService = app(ProductBatchService::class);

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        try {
            Log::channel('batch_upload')->warning("log id : {$this->logId} 開始寫入");
            // $logData = $batchService->getById($this->logId);
            // $result = $batchService->batchUpload($logData);
            Log::channel('batch_upload')->warning("log id : {$this->logId} 寫入完成");
        } catch (Exception $e) {
            $this->batchService->updateStatusById($this->logId, BatchUploadLogStatus::STATUS_FAILED);
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
            // TODO 更新狀態
            $this->batchService->updateStatusById($this->logId, BatchUploadLogStatus::STATUS_FAILED);
            Log::channel('batch_upload')->warning("failed try log id : {$this->logId} 失敗".$e->getMessage());
        } catch (Exception $e) {
            Log::channel('batch_upload')->warning("failed catch log id : {$this->logId} 失敗".$e->getMessage());
        }
    }
}

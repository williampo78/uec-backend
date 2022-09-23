<?php

namespace App\Services;

use App\Enums\BatchUploadLogStatus;
use App\Jobs\ProductImportJob;
use App\Models\BatchUploadLog;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductBatchService
{
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
}

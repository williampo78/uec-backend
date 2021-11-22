<?php
//圖片上傳服務

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Storage;

class ImageUploadService
{

    public function __construct()
    {

    }
    /**
     * $file 可以是 array OR 單筆的 file obj
     * $path 指定路徑
     */
    public function uploadImage($file, $path)
    {
        $storage_s3 = Storage::disk('s3');
        $result = [];
        try {
            if (is_array($file)) {
                foreach ($file as $key => $obj) {
                    $result['image'][$key] = $storage_s3->put($path, $obj, 'public');
                }
            } else {
                $result['image'] = $storage_s3->put($path, $file, 'public');
            }
            $result['status'] = true;
        } catch (\Exception $e) {
            Log::error($e);
            $result['status'] = false;
        }
        return $result;
    }
    /**
     *  經由 Storage find Img URl
     */
    public function getImage($file_name = null)
    {
        if (empty($file_name)) {
            return false ;
        } else {
            $storage_s3 = Storage::disk('s3');
            return $storage_s3->url($file_name);
        }
    }

}

<?php
//圖片上傳服務

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Image;

class ImageUploadService
{

    public function __construct()
    {

    }
    /**
     * $file 可以是 array OR 單筆的 file obj
     * $path 指定路徑
     */
    public function uploadImage($file, $path, $type = null)
    {
        $storage_s3 = Storage::disk('s3');
        $result = [];
        try {
            switch ($type) {
                case 'product': //要執行裁切
                    if (is_array($file)) {
                        foreach ($file as $key => $obj) {
                            $img = Image::make($obj);
                            $img->resize('480', '480');
                            $resource = $img->stream()->detach();
                            $typeName = '.'.explode(".",$obj->getClientOriginalName())[1] ?? '';
                            $imageName = '480X480_'.uniqid(date('YmdHis')).$typeName;
                            Storage::disk('s3')->put('/'.$path.'/' . $imageName, $resource, 'public');
                            $result['image'][$key] = $path.'/' . $imageName;
                        }
                    } else {
                        $img = Image::make($file);
                        $img->resize('480', '480');
                        $resource = $img->stream()->detach();
                        $typeName = '.'.explode(".",$file->getClientOriginalName())[1] ?? '';
                        $imageName = '480X480_' . uniqid(date('YmdHis')) . $typeName;
                        Storage::disk('s3')->put('/'.$path.'/' . $imageName, $resource, 'public');
                        $result['image'] = $path.'/' . $imageName;
                    }
                    break;

                default:
                    if (is_array($file)) {
                        foreach ($file as $key => $obj) {
                            $result['image'][$key] = $storage_s3->put($path, $obj, 'public');
                        }
                    } else {
                        $result['image'] = $storage_s3->put($path, $file, 'public');
                    }
                    break;
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
            $exists = Storage::disk('s3')->exists($file_name);
            if($exists){
                return Storage::disk('s3')->url($file_name);
            }else{
                return false ;
            }
        }
    }
    public function getSize($file_name)
    {
        $storage_s3 = Storage::disk('s3');
        $exists = Storage::disk('s3')->exists($file_name);
        if ($exists) {
            return $storage_s3->size($file_name);
        } else {
            return false;
        }
    }
    public function DelPhoto($file_name)
    {
        $storage_s3 = Storage::disk('s3');
        return $storage_s3->delete($file_name);
    }
}

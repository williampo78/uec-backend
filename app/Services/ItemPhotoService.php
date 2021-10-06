<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ItemPhotoService
{
    private $agent_id;
    public function __construct()
    {
        $this->agent_id = Auth::user()->agent_id;
    }

    //未完成 圖片上傳增加資料進ItemPhoto
    public function insertData($data){
        $data['agent_id'] = $this->agent_id;
        $data['created_at'] = date("Y-m-d H:i:s");
//        $data['sort'] = $photo_count;

        Item::insert($data);

        try {
            $result = ItemPhoto::insert($data);
        }catch (\Exception $e){
            Log::info($e);
        }

        return $result;
    }
}

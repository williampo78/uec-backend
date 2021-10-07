<?php

namespace App\Services;

use App\Models\AgentConfig;
use App\Models\Category;
use App\Models\PrimaryCategory;
use Illuminate\Support\Facades\Auth;

class CategoryService
{
    public function __construct()
    {
    }
    //取得使用者權限

    public function getCategory()
    {
        $agent_id = Auth::user()->agent_id;
        $category = Category::where('agent_id' , $agent_id)->get();

        return $category;
    }

    public function getPrimaryCategoryForList(){
        $rs = PrimaryCategory::all();
        $data = [];
        foreach ($rs as $k => $v){
            $data[$v['id']] = [
                'name' => $v['name'] ,
                'number' => $v['number']
            ];
        }

        return $data;
    }
}

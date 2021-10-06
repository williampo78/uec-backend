<?php

namespace App\Services;

use App\Models\AgentConfig;
use App\Models\Category;
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
}

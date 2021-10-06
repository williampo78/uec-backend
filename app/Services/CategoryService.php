<?php

namespace App\Services;

use App\Models\AgentConfig;
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
        $agentConfig = AgentConfig::where('agent_id' , $agent_id)->where('key' , 'setting')->first()->toArray();

        return $agentConfig;
    }
}

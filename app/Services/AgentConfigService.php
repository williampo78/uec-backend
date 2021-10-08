<?php

namespace App\Services;



use App\Models\AgentConfig;
use Illuminate\Support\Facades\Auth;

class AgentConfigService
{
    public function __construct()
    {
    }

    public function getAgentConfig()
    {
        $agent_id = Auth::user()->agent_id;
        $agentConfig = AgentConfig::where('agent_id' , $agent_id)->where('key' , 'setting')->first()->toArray();
        return $agentConfig;
    }
}

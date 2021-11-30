<?php

namespace App\Services;

use SqlFormatter;
use App\Models\Lookup_values_v;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LookupValuesVService
{
    public function getApplicablePage()
    {
        $agent_id = Auth::user()->agent_id;

        $result = Lookup_values_v::select('code', 'description')
            ->where('agent_id', $agent_id)
            ->where('type_code', 'APPLICABLE_PAGE')
            ->orderBy("code", "asc")
            ->get();

        return $result;
    }
}

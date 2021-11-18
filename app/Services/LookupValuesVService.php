<?php

namespace App\Services;

use App\Models\Lookup_values_v;
use Illuminate\Support\Facades\Auth;

class LookupValuesVService
{
    public function getApplicablePage()
    {
        $agent_id = Auth::user()->agent_id;

        return Lookup_values_v::select('code', 'description')
            ->where([
                ['agent_id', '=', $agent_id],
                ['type_code', '=', 'APPLICABLE_PAGE'],
            ])
            ->orderBy("code", "asc")
            ->get();
    }
}

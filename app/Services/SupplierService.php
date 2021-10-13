<?php

namespace App\Services;

use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SupplierService
{
    public function __construct()
    {
    }

    public function getSupplier()
    {
        $agent_id = Auth::user()->agent_id;
        return Supplier::where('agent_id', $agent_id)->where('active', 1)->get();
    }

    public function addSupplier($inputdata)
    {
        $inputdata['agent_id'] = Auth::user()->agent_id;
        try {
            return Supplier::create($inputdata);
        } catch (\Exception $e) {
            Log::info($e);
        }
    }

    public function showSupplier($id)
    {
        return Supplier::where('id', $id)->get()->first();
    }

    public function updateSupplier($input,$id)
    {
        unset($input['_token']);
        unset($input['_method']);

        return Supplier::where('id', $id)->update($input);
    }
}

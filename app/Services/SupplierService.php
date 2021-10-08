<?php

namespace App\Services;



use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;

class SupplierService
{
    public function __construct()
    {
    }

    public function getSupplier()
    {
        $agent_id = Auth::user()->agent_id;
        return Supplier::where('agent_id' , $agent_id)->where('active',1)->get();
    }
}

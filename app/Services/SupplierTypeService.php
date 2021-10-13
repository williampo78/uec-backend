<?php

namespace App\Services;

use App\Models\SupplierType;
use Illuminate\Support\Facades\Log;

class SupplierTypeService
{
    public function __construct()
    {
    }

    public function getSupplierType($request = null)
    {
        // dump($request->input());
        // order_by DESC OR ASC
        // who order_by ? name or code ?
        // page function
        // view data Number

        return SupplierType::All();
    }
    public function Get($SupplierType_id)
    {
        try {
            return SupplierType::find($SupplierType_id)->toArray();

        } catch (\Exception $e) {
            Log::info($e);
        }
    }
    public function Add($inputData)
    {
        try {
            return $user = SupplierType::create(
                ['name' => $inputData['name'], 'code' => $inputData['code'], 'agent_id' => '1'],
            );
        } catch (\Exception $e) {
            Log::info($e);
        }

    }
    public function Update($inputData, $id)
    {
        $SupplierType = SupplierType::find($id);
        $SupplierType->name = $inputData['name'];
        $SupplierType->code = $inputData['code'];
        try {
            return $SupplierType->save();
        } catch (\Exception $e) {
            Log::info($e);
        }
    }
}

<?php

namespace App\Services;

use App\Models\SupplierType;

class SupplierTypeService
{
    public function __construct()
    {
    }

    public function Get_All()
    {
        return SupplierType::All()->toArray();
    }
    public function Get($SupplierType_id)
    {
        return SupplierType::find($SupplierType_id)->toArray();
    }
    public function Add($inputData)
    {
        return $user = SupplierType::create(
            ['name' => $inputData['name'], 'code' => $inputData['code'], 'agent_id' => '1'],
        );
    }
    public function Update($inputData, $id)
    {
        $SupplierType = SupplierType::find($id);
        $SupplierType->name = $inputData['name'];
        $SupplierType->code = $inputData['code'];
        return $SupplierType->save();

    }
}

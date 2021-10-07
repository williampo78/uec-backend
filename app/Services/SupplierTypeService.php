<?php

namespace App\Services;

use App\Models\SupplierType;

class SupplierTypeService
{
    public function __construct()
    {
    }

    public function Get_All($request)
    {
        // dump($request->input());
        // order_by DESC OR ASC 
        // who order_by ? name or code ?
        // page function 
        // view data Number 
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

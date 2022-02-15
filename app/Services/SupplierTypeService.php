<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\SupplierType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

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
        $result = [];
        $now = Carbon::now();
        try {
            SupplierType::insert(
                [
                    'name' => $inputData['name'],
                    'code' => $inputData['code'],
                    'agent_id' => Auth::user()->agent_id,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ],
            );
            $result['status'] = true;
        } catch (\Exception $e) {
            $result['status'] = false;
            $result['error_code'] = $e;
            Log::info($e);
        }
        return $result;
    }
    public function Update($inputData, $id)
    {
        $result = [];
        $now = Carbon::now();
        try {
            $SupplierType = SupplierType::find($id);
            $SupplierType->name = $inputData['name'];
            $SupplierType->code = $inputData['code'];
            $SupplierType->updated_at =  $now; 
            $SupplierType->updated_by = Auth::user()->id ; 
            $SupplierType->save();
            $result['status'] = true;
        } catch (\Exception $e) {
            $result['status'] = false;
            $result['error_code'] = $e;
            Log::info($e);
        }
        return $result ;
    }
}

<?php

namespace App\Services;

use App\Models\SupplierType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SupplierTypeService
{
    /**
     * 取得供應商類別
     *
     * @return Collection
     */
    public function getSupplierTypes(): Collection
    {
        $user = auth()->user();
        $supplierTypes = SupplierType::where('agent_id', $user->agent_id)->get();

        return $supplierTypes;
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
            Log::error($e->getMessage());
        }
    }

    public function Add($inputData)
    {
        $result = [];
        try {
            SupplierType::insert([
                'name' => $inputData['name'],
                'code' => $inputData['code'],
                'agent_id' => Auth::user()->agent_id,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $result['status'] = true;
        } catch (\Exception $e) {
            $result['status'] = false;
            $result['error_code'] = $e;
            Log::error($e->getMessage());
        }

        return $result;
    }

    public function Update($inputData, $id)
    {
        $result = [];
        try {
            $SupplierType = SupplierType::find($id);
            $SupplierType->name = $inputData['name'];
            $SupplierType->code = $inputData['code'];
            $SupplierType->updated_at = now();
            $SupplierType->updated_by = Auth::user()->id;
            $SupplierType->save();

            $result['status'] = true;
        } catch (\Exception $e) {
            $result['status'] = false;
            $result['error_code'] = $e;
            Log::error($e->getMessage());
        }

        return $result;
    }
}

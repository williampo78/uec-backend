<?php

namespace App\Services;

use App\Models\Supplier;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SupplierService
{
    /**
     * 取得所有供應商
     *
     * @param array $queryData
     * @return Collection
     */
    public function getSuppliers(array $queryData = []): Collection
    {
        $user = auth()->user();
        $suppliers = Supplier::where('agent_id', $user->agent_id);

        // 檢查使用者是否為供應商
        if (!empty($queryData['check_user_is_supplier'])) {
            if (isset($user->supplier_id)) {
                $suppliers = $suppliers->where('id', $user->supplier_id);
            }
        }

        return $suppliers->get();
    }

    public function addSupplier($inputdata)
    {
        $inputdata['agent_id'] = Auth::user()->agent_id;
        $inputdata['created_by'] = Auth::user()->id;
        $inputdata['updated_by'] = Auth::user()->id;
        DB::beginTransaction();
        try {
            DB::commit();
            Supplier::create($inputdata);
            $result = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::warning($e->getMessage());
            $result = false;
        }
        return $result;

    }
    public function checkDisplayNumber($DisplayNumber)
    {
        $check = Supplier::where('display_number', $DisplayNumber)->count();
        return $check == 0;
    }

    public function showSupplier($id)
    {
        return Supplier::where('id', $id)->get()->first();
    }

    public function updateSupplier($input, $id)
    {
        unset($input['_token']);
        unset($input['_method']);

        return Supplier::where('id', $id)->update($input);
    }
    public function getPaymentTerms()
    {
        return DB::table('lookup_values')->where('lookup_type_id', '9')->get();
    }

}

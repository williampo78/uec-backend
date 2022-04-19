<?php

namespace App\Services;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SupplierService
{
    public function getSuppliers($query_data = [])
    {
        $agent_id = Auth::user()->agent_id;
        $result = [];

        $result = Supplier::where('agent_id', $agent_id);

        if (isset($query_data['active'])) {
            if ($query_data['active'] == 1) {
                $result = $result->where('active', 1);
            } else {
                $result = $result->where('active', 0);
            }
        }

        $result = $result->get();

        return $result;
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

    /**
     * 取得供應商table列表
     *
     * @param array $queryData
     * @return Collection
     */
    public function getTableList(array $queryData = []): Collection
    {
        $user = auth()->user();

        $suppliers = Supplier::with(['paymentTerm'])->where('agent_id', $user->agent_id);

        if (isset($queryData['supplier_type_id'])) {
            $suppliers = $suppliers->whereHas('supplierType', function (Builder $query) use ($queryData) {
                return $query->where('id', $queryData['supplier_type_id']);
            });
        }

        if (isset($queryData['display_number_or_name'])) {
            $suppliers = $suppliers->where(function ($query) use ($queryData) {
                return $query->where('display_number', 'LIKE', '%' . $queryData['display_number_or_name'] . '%')
                    ->orWhere('name', 'LIKE', '%' . $queryData['display_number_or_name'] . '%');
            });
        }

        if (isset($queryData['company_number'])) {
            $suppliers = $suppliers->where('company_number', $queryData['company_number']);
        }

        if (isset($queryData['active'])) {
            $suppliers = $suppliers->where('active', $queryData['active']);
        }

        return $suppliers->oldest('display_number')->get();
    }
}

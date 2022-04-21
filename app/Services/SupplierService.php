<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Supplier;
use App\Models\SupplierContract;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\SupplierContractTerm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

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

    public function createSupplier($inputData)
    {
        $user = auth()->user();
        $result = false;

        DB::beginTransaction();
        try {
            // 新增供應商
            $supplierData = [
                'agent_id' => $user->agent_id,
                'supplier_type_id' => $inputData['supplier_type_id'],
                'payment_term' => $inputData['payment_term'] ?? null,
                'display_number' => $inputData['display_number'],
                'company_number' => $inputData['company_number'],
                'name' => $inputData['name'],
                'short_name' => $inputData['short_name'],
                'tax_type' => $inputData['tax_type'],
                'contact_name' => $inputData['contact_name'] ?? null,
                'email' => $inputData['email'] ?? null,
                'telephone' => $inputData['telephone'] ?? null,
                'fax' => $inputData['fax'] ?? null,
                'cell_phone' => $inputData['cell_phone'] ?? null,
                'postal_code' => $inputData['postal_code'] ?? null,
                'address' => $inputData['address'] ?? null,
                'address2' => $inputData['address2'] ?? null,
                'address3' => $inputData['address3'] ?? null,
                'address4' => $inputData['address4'] ?? null,
                'address5' => $inputData['address5'] ?? null,
                'bank_name' => $inputData['bank_name'] ?? null,
                'bank_branch' => $inputData['bank_branch'] ?? null,
                'remark' => $inputData['remark'] ?? null,
                'active' => $inputData['active'],
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ];
            $newSupplier = Supplier::create($supplierData);

            // 新增聯絡人
            if (isset($inputData['contacts'])) {
                foreach ($inputData['contacts'] as $contact) {
                    $contactData = [
                        'table_name' => 'Supplier',
                        'table_id' => $newSupplier->id,
                        'name' => $contact['name'],
                        'email' => $contact['email'],
                        'telephone' => $contact['telephone'],
                        'fax' => $contact['fax'],
                        'cell_phone' => $contact['cell_phone'],
                        'remark' => $contact['remark'],
                    ];
                    Contact::create($contactData);
                }
            }

            // 新增供應商合約
            $supplierContractData = [
                'supplier_id' => $newSupplier->id,
                'date_from' => $inputData['date_from'],
                'date_to' => $inputData['date_to'],
                'status_code' => $inputData['status_code'],
                'billing_cycle' => $inputData['billing_cycle'],
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ];
            $newSupplierContract = SupplierContract::create($supplierContractData);

            // 新增供應商合約條款明細
            if (isset($inputData['contract_terms'])) {
                foreach ($inputData['contract_terms'] as $contactTerm) {
                    $contactTermData = [
                        'supplier_contract_id' => $newSupplierContract->id,
                        'term_code' => $contactTerm['term_code'],
                        'term_value' => $contactTerm['term_value'] ?? null,
                        'created_by' => $user->id,
                        'updated_by' => $user->id,
                    ];
                    SupplierContractTerm::create($contactTermData);
                }
            }

            DB::commit();
            $result = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
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

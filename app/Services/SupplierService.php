<?php

namespace App\Services;

use App\Models\Contact;
use App\Models\Supplier;
use App\Models\SupplierContract;
use App\Models\SupplierContractTerm;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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

    public function createSupplier(array $inputData): bool
    {
        $user = auth()->user();
        $result = false;

        DB::beginTransaction();
        try {
            // 新增供應商
            $createdSupplier = Supplier::create([
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
            ]);

            // 新增聯絡人
            if (isset($inputData['contacts'])) {
                foreach ($inputData['contacts'] as $contact) {
                    Contact::create([
                        'table_name' => 'Supplier',
                        'table_id' => $createdSupplier->id,
                        'name' => $contact['name'],
                        'email' => $contact['email'],
                        'telephone' => $contact['telephone'],
                        'fax' => $contact['fax'],
                        'cell_phone' => $contact['cell_phone'],
                        'remark' => $contact['remark'],
                    ]);
                }
            }

            // 新增供應商合約
            $createdSupplierContract = SupplierContract::create([
                'supplier_id' => $createdSupplier->id,
                'date_from' => $inputData['date_from'],
                'date_to' => $inputData['date_to'],
                'status_code' => $inputData['status_code'],
                'billing_cycle' => $inputData['billing_cycle'],
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            // 新增供應商合約條款明細
            if (isset($inputData['contract_terms'])) {
                foreach ($inputData['contract_terms'] as $contactTerm) {
                    SupplierContractTerm::create([
                        'supplier_contract_id' => $createdSupplierContract->id,
                        'term_code' => $contactTerm['term_code'],
                        'term_value' => $contactTerm['term_value'] ?? null,
                        'created_by' => $user->id,
                        'updated_by' => $user->id,
                    ]);
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

    /**
     * 供應商編號是否存在
     *
     * @param string $displayNumber
     * @param integer|null $excludeSupplierId
     * @return boolean
     */
    public function displayNumberExists(string $displayNumber, int $excludeSupplierId = null): bool
    {
        $user = auth()->user();

        $supplier = Supplier::where('agent_id', $user->agent_id)
            ->where('display_number', $displayNumber);

        if (isset($excludeSupplierId)) {
            $supplier = $supplier->where('id', '!=', $excludeSupplierId);
        }

        if ($supplier->count() < 1) {
            return false;
        }

        return true;
    }

    public function updateSupplier(array $inputData): bool
    {
        $user = auth()->user();
        $result = false;

        DB::beginTransaction();
        try {
            // 更新供應商
            Supplier::findOrFail($inputData['id'])->update([
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
                'updated_by' => $user->id,
            ]);

            $updatedContactIds = [];
            // 新增或更新聯絡人
            if (isset($inputData['contacts'])) {
                foreach ($inputData['contacts'] as $contact) {
                    $updatedContact = Contact::updateOrCreate([
                        'id' => $contact['contact_id'],
                        'table_name' => 'Supplier',
                        'table_id' => $inputData['id'],
                    ], [
                        'name' => $contact['name'],
                        'email' => $contact['email'],
                        'telephone' => $contact['telephone'],
                        'fax' => $contact['fax'],
                        'cell_phone' => $contact['cell_phone'],
                        'remark' => $contact['remark'],
                    ]);

                    $updatedContactIds[] = $updatedContact->id;
                }
            }

            // 刪除聯絡人
            Contact::where('table_id', $inputData['id'])->whereNotIn('id', $updatedContactIds)->delete();

            // 更新供應商合約
            SupplierContract::findOrFail($inputData['supplier_contract_id'])->update([
                'date_from' => $inputData['date_from'],
                'date_to' => $inputData['date_to'],
                'status_code' => $inputData['status_code'],
                'billing_cycle' => $inputData['billing_cycle'],
                'updated_by' => $user->id,
            ]);

            $updatedSupplierContractTermIds = [];
            // 新增或更新供應商合約條款明細
            if (isset($inputData['contract_terms'])) {
                foreach ($inputData['contract_terms'] as $contactTerm) {
                    $supplierContractTerm = SupplierContractTerm::where('supplier_contract_id', $inputData['supplier_contract_id'])
                        ->where('term_code', $contactTerm['term_code'])
                        ->first();

                    if (!isset($supplierContractTerm)) {
                        $createdSupplierContractTerm = SupplierContractTerm::create([
                            'supplier_contract_id' => $inputData['supplier_contract_id'],
                            'term_code' => $contactTerm['term_code'],
                            'term_value' => $contactTerm['term_value'] ?? null,
                            'created_by' => $user->id,
                            'updated_by' => $user->id,
                        ]);

                        $updatedSupplierContractTermIds[] = $createdSupplierContractTerm->id;
                    } else {
                        SupplierContractTerm::findOrFail($supplierContractTerm->id)->update([
                            'term_value' => $contactTerm['term_value'] ?? null,
                            'updated_by' => $user->id,
                        ]);

                        $updatedSupplierContractTermIds[] = $supplierContractTerm->id;
                    }
                }
            }

            // 刪除供應商合約條款明細
            SupplierContractTerm::where('supplier_contract_id', $inputData['supplier_contract_id'])->whereNotIn('id', $updatedSupplierContractTermIds)->delete();

            DB::commit();
            $result = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
        }

        return $result;
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

    /**
     * 取得供應商
     *
     * @param integer $id
     * @return Model
     */
    public function getSupplierById(int $id): Model
    {
        $user = auth()->user();
        $supplier = Supplier::with([
            'contacts',
            'supplierContract',
            'supplierContract.supplierContractTerms',
        ])->where('agent_id', $user->agent_id)->find($id);

        return $supplier;
    }
}

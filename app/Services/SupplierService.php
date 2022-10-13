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
     * @param array $filter
     * @return Collection
     */
    public function getSuppliers(array $filter = []): Collection
    {
        $user = auth()->user();

        return Supplier::when(isset($user), function ($query) use ($user) {
                $query->where('agent_id', $user->agent_id);
            })
            // 狀態
            ->when(isset($filter['active']), function ($query) use ($filter) {
                $query->where('active', $filter['active']);
            })
            ->get();
    }

    /**
     * 新增供應商
     *
     * @param array $data
     * @return boolean
     */
    public function createSupplier(array $data): bool
    {
        $user = auth()->user();
        $result = false;

        DB::beginTransaction();
        try {
            // 新增供應商
            $createdSupplier = Supplier::create([
                'agent_id' => $user->agent_id,
                'supplier_type_id' => $data['supplier_type_id'],
                'payment_term' => $data['payment_term'] ?? null,
                'display_number' => $data['display_number'],
                'company_number' => $data['company_number'],
                'name' => $data['name'],
                'short_name' => $data['short_name'],
                'tax_type' => $data['tax_type'],
                'contact_name' => $data['contact_name'] ?? null,
                'email' => $data['email'] ?? null,
                'telephone' => $data['telephone'] ?? null,
                'fax' => $data['fax'] ?? null,
                'cell_phone' => $data['cell_phone'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'address' => $data['address'] ?? null,
                'address2' => $data['address2'] ?? null,
                'address3' => $data['address3'] ?? null,
                'address4' => $data['address4'] ?? null,
                'address5' => $data['address5'] ?? null,
                'bank_name' => $data['bank_name'] ?? null,
                'bank_branch' => $data['bank_branch'] ?? null,
                'bank_account_number' => $data['bank_account_number'] ?? null,
                'remark' => $data['remark'] ?? null,
                'active' => $data['active'],
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            // 新增聯絡人
            if (isset($data['contacts'])) {
                foreach ($data['contacts'] as $contact) {
                    Contact::create([
                        'table_name' => 'Supplier',
                        'table_id' => $createdSupplier->id,
                        'name' => $contact['name'],
                        'email' => $contact['email'] ?? null,
                        'telephone' => $contact['telephone'] ?? null,
                        'fax' => $contact['fax'] ?? null,
                        'cell_phone' => $contact['cell_phone'] ?? null,
                        'remark' => $contact['remark'] ?? null,
                        'created_by' => $user->id,
                        'updated_by' => $user->id,
                    ]);
                }
            }

            // 新增供應商合約
            $createdSupplierContract = SupplierContract::create([
                'supplier_id' => $createdSupplier->id,
                'date_from' => $data['date_from'] ?? null,
                'date_to' => $data['date_to'] ?? null,
                'status_code' => $data['status_code'] ?? null,
                'billing_cycle' => $data['billing_cycle'] ?? null,
                'created_by' => $user->id,
                'updated_by' => $user->id,
            ]);

            // 新增供應商合約條款明細
            if (isset($data['contract_terms'])) {
                foreach ($data['contract_terms'] as $contactTerm) {
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

    /**
     * 更新供應商
     *
     * @param integer $id
     * @param array $data
     * @return boolean
     */
    public function updateSupplier(int $id, array $data): bool
    {
        $user = auth()->user();
        $result = false;

        DB::beginTransaction();
        try {
            // 更新供應商
            Supplier::findOrFail($id)->update([
                'supplier_type_id' => $data['supplier_type_id'],
                'payment_term' => $data['payment_term'] ?? null,
                'display_number' => $data['display_number'],
                'company_number' => $data['company_number'],
                'name' => $data['name'],
                'short_name' => $data['short_name'],
                'tax_type' => $data['tax_type'],
                'contact_name' => $data['contact_name'] ?? null,
                'email' => $data['email'] ?? null,
                'telephone' => $data['telephone'] ?? null,
                'fax' => $data['fax'] ?? null,
                'cell_phone' => $data['cell_phone'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'address' => $data['address'] ?? null,
                'address2' => $data['address2'] ?? null,
                'address3' => $data['address3'] ?? null,
                'address4' => $data['address4'] ?? null,
                'address5' => $data['address5'] ?? null,
                'bank_name' => $data['bank_name'] ?? null,
                'bank_branch' => $data['bank_branch'] ?? null,
                'bank_account_number' => $data['bank_account_number'] ?? null,
                'remark' => $data['remark'] ?? null,
                'active' => $data['active'],
                'updated_by' => $user->id,
            ]);

            $updatedContactIds = [];
            // 新增或更新聯絡人
            if (isset($data['contacts'])) {
                $originContactIds = Contact::where('table_name', 'Supplier')->where('table_id', $id)->pluck('id');
                foreach ($data['contacts'] as $contact) {
                    // 新增
                    if (!$originContactIds->contains($contact['id'])) {
                        $createdContact = Contact::create([
                            'table_name' => 'Supplier',
                            'table_id' => $id,
                            'name' => $contact['name'],
                            'email' => $contact['email'] ?? null,
                            'telephone' => $contact['telephone'] ?? null,
                            'fax' => $contact['fax'] ?? null,
                            'cell_phone' => $contact['cell_phone'] ?? null,
                            'remark' => $contact['remark'] ?? null,
                            'created_by' => $user->id,
                            'updated_by' => $user->id,
                        ]);

                        $updatedContactIds[] = $createdContact->id;
                    }
                    // 更新
                    else {
                        Contact::findOrFail($contact['id'])->update([
                            'name' => $contact['name'],
                            'email' => $contact['email'] ?? null,
                            'telephone' => $contact['telephone'] ?? null,
                            'fax' => $contact['fax'] ?? null,
                            'cell_phone' => $contact['cell_phone'] ?? null,
                            'remark' => $contact['remark'] ?? null,
                            'updated_by' => $user->id,
                        ]);

                        $updatedContactIds[] = $contact['id'];
                    }
                }
            }

            // 刪除聯絡人
            Contact::where('table_name', 'Supplier')->where('table_id', $id)->whereNotIn('id', $updatedContactIds)->delete();

            $supplierContractId = null;
            if (isset($data['supplier_contract_id'])) {
                // 更新供應商合約
                SupplierContract::findOrFail($data['supplier_contract_id'])->update([
                    'date_from' => $data['date_from'] ?? null,
                    'date_to' => $data['date_to'] ?? null,
                    'status_code' => $data['status_code'] ?? null,
                    'billing_cycle' => $data['billing_cycle'] ?? null,
                    'updated_by' => $user->id,
                ]);
                $supplierContractId = $data['supplier_contract_id'];
            } else {
                // 新增供應商合約
                $createdSupplierContract = SupplierContract::create([
                    'supplier_id' => $id,
                    'date_from' => $data['date_from'] ?? null,
                    'date_to' => $data['date_to'] ?? null,
                    'status_code' => $data['status_code'] ?? null,
                    'billing_cycle' => $data['billing_cycle'] ?? null,
                    'created_by' => $user->id,
                    'updated_by' => $user->id,
                ]);
                $supplierContractId = $createdSupplierContract->id;
            }

            $updatedSupplierContractTermIds = [];
            // 新增或更新供應商合約條款明細
            if (isset($data['contract_terms'])) {
                foreach ($data['contract_terms'] as $contactTerm) {
                    $supplierContractTerm = SupplierContractTerm::where('supplier_contract_id', $supplierContractId)
                        ->where('term_code', $contactTerm['term_code'])
                        ->first();

                    if (!isset($supplierContractTerm)) {
                        $createdSupplierContractTerm = SupplierContractTerm::create([
                            'supplier_contract_id' => $supplierContractId,
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
            SupplierContractTerm::where('supplier_contract_id', $supplierContractId)->whereNotIn('id', $updatedSupplierContractTermIds)->delete();

            DB::commit();
            $result = true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
        }

        return $result;
    }

    /**
     * 取得供應商列表
     *
     * @param array $filter
     * @return Collection
     */
    public function getList(array $filter = []): Collection
    {
        $user = auth()->user();

        return Supplier::with([
            'paymentTerm',
        ])
            ->where('agent_id', $user->agent_id)
            ->when(isset($filter['supplier_type_id']), function ($query) use ($filter) {
                $query->whereHas('supplierType', function (Builder $query) use ($filter) {
                    $query->where('id', $filter['supplier_type_id']);
                });
            })
            ->when(isset($filter['display_number_or_name']), function ($query) use ($filter) {
                $query->where(function ($query) use ($filter) {
                    $query->where('display_number', 'LIKE', "%{$filter['display_number_or_name']}%")
                        ->orWhere('name', 'LIKE', "%{$filter['display_number_or_name']}%");
                });
            })
            ->when(isset($filter['company_number']), function ($query) use ($filter) {
                $query->where('company_number', $filter['company_number']);
            })
            ->when(isset($filter['active']), function ($query) use ($filter) {
                $query->where('active', $filter['active']);
            })
            ->oldest('display_number')
            ->get();
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

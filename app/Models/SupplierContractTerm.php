<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierContractTerm extends Model
{
    use HasFactory;

    protected $table = 'supplier_contract_terms';
    protected $guarded = [];

    /**
     * 建立與供應商合約的關聯
     */
    public function supplierContract()
    {
        return $this->belongsTo(SupplierContract::class, 'supplier_contract_id');
    }
}

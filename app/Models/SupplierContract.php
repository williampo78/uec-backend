<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierContract extends Model
{
    use HasFactory;

    protected $table = 'supplier_contracts';
    protected $guarded = [];

    /**
     * 建立與供應商的關聯
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * 建立與供應商合約條款明細的關聯
     */
    public function supplierContractTerms()
    {
        return $this->hasMany(SupplierContractTerm::class, 'supplier_contract_id');
    }
}

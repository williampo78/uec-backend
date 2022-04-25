<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'supplier';
    protected $guarded = [];

    /**
     * 建立與使用者的關聯
     */
    public function users()
    {
        return $this->hasMany(User::class, 'supplier_id');
    }

    /**
     * 建立與供應商類別的關聯
     */
    public function supplierType()
    {
        return $this->belongsTo(SupplierType::class, 'supplier_type_id');
    }

    /**
     * 建立與付款方式設定檔的關聯
     */
    public function paymentTerm()
    {
        return $this->belongsTo(LookupValuesV::class, 'payment_term', 'code')->where('type_code', 'PAYMENT_TERMS');
    }

    /**
     * 建立與聯絡人的關聯
     */
    public function contacts()
    {
        return $this->hasMany(Contact::class, 'table_id')->where('table_name', 'Supplier');
    }

    /**
     * 建立與供應商合約的關聯
     */
    public function supplierContract()
    {
        return $this->hasOne(SupplierContract::class, 'supplier_id');
    }
}

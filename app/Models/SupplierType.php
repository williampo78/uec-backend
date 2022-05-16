<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierType extends Model
{
    use HasFactory;

    protected $table = 'supplier_type';
    protected $guarded = [];

    /**
     * 建立與供應商的關聯
     */
    public function suppliers()
    {
        return $this->hasMany(Supplier::class, 'supplier_type_id');
    }
}

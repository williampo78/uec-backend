<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiscStockRequestSupplier extends Model
{
    use HasFactory;

    protected $table = 'misc_stock_request_suppliers';
    protected $guarded = [];

    /**
     * 建立與庫存進出申請單明細的關聯
     */
    public function miscStockRequestDetails()
    {
        return $this->hasMany(MiscStockRequestDetail::class, 'misc_stock_request_sup_id');
    }

    /**
     * 建立與供應商的關聯
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * 建立與庫存進出申請單的關聯
     */
    public function miscStockRequest()
    {
        return $this->belongsTo(MiscStockRequest::class, 'misc_stock_request_id');
    }

    /**
     * 建立與簽核者的關聯
     */
    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewer');
    }
}

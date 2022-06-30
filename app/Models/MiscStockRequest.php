<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiscStockRequest extends Model
{
    use HasFactory;

    protected $table = 'misc_stock_requests';
    protected $guarded = [];

    /**
     * 建立與倉庫的關聯
     */
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    /**
     * 建立與庫存進出申請單明細的關聯
     */
    public function miscStockRequestDetails()
    {
        return $this->hasMany(MiscStockRequestDetail::class, 'misc_stock_request_id');
    }

    /**
     * 建立與供應商的關聯
     */
    public function suppliers()
    {
        return $this->belongsToMany(Supplier::class, 'misc_stock_request_suppliers', 'misc_stock_request_id', 'supplier_id')
            ->withTimestamps()
            ->withPivot('id', 'status_code', 'expected_qty', 'expected_amount', 'actual_qty', 'actual_amount', 'reviewer', 'review_at', 'review_result', 'review_remark');
    }
}

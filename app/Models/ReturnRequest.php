<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnRequest extends Model
{
    use HasFactory;

    protected $table = 'return_requests';
    protected $guarded = [];

    /**
     * 建立與訂單的關聯
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * 建立與退貨申請單明細的關聯
     */
    public function returnRequestDetails()
    {
        return $this->hasMany(ReturnRequestDetail::class, 'return_request_id');
    }

    /*
     * 建立與退貨檢驗單的關聯
     */
    public function returnExamination()
    {
        return $this->hasMany(ReturnExamination::class, 'request_no','request_no');
    }

    public function returnOrderDetails()
    {
        return $this->hasMany(ReturnOrderDetail::class, 'return_request_id');
    }
}

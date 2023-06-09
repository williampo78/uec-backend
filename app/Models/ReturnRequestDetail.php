<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnRequestDetail extends Model
{
    use HasFactory;

    protected $table = 'return_request_details';
    protected $guarded = [];

    /**
     * 建立與退貨申請單的關聯
     */
    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class, 'return_request_id');
    }

    public function productItem():belongsTo
    {
        return $this->belongsTo(ProductItem::class, 'product_item_id');
    }
}

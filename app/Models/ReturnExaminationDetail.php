<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnExaminationDetail extends Model
{
    use HasFactory;

    protected $table = 'return_examination_details';
    protected $guarded = [];

    public function productItem():belongsTo
    {
        return $this->belongsTo(ProductItem::class, 'product_item_id');
    }

    public function ReturnRequestDetail():belongsTo
    {
        return $this->belongsTo(ReturnRequestDetail::class, 'return_request_detail_id');
    }
}

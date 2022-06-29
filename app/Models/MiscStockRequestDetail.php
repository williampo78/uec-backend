<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiscStockRequestDetail extends Model
{
    use HasFactory;

    protected $table = 'misc_stock_request_details';
    protected $guarded = [];

    /**
     * 建立與庫存進出申請單的關聯
     */
    public function miscStockRequest()
    {
        return $this->belongsTo(MiscStockRequest::class, 'misc_stock_request_id');
    }

    /**
     * 建立與商品品項的關聯
     */
    public function productItem()
    {
        return $this->belongsTo(ProductItem::class, 'product_item_id');
    }
}

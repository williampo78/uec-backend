<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $table = 'warehouse';

    /**
     * 建立與商品品項的關聯
     */
    public function productItems()
    {
        return $this->belongsToMany(ProductItem::class, 'warehouse_stock', 'warehouse_id', 'product_item_id')->withTimestamps()->withPivot('stock_qty');
    }
}

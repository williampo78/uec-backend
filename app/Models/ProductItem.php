<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductItem extends Model
{
    use HasFactory;

    protected $table = 'product_items';
    protected $guarded = [];

   /**
     * 建立與訂單明細的關聯
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'product_item_id');
    }

    /**
     * 建立與商品的關聯
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * 建立與倉庫的關聯
     */
    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class, 'warehouse_stock', 'product_item_id', 'warehouse_id')->withTimestamps()->withPivot('stock_qty');
    }
}

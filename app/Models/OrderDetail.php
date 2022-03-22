<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'order_details';
    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productItem()
    {
        return $this->belongsTo(ProductItem::class, 'product_item_id');
    }

    /**
     * 取得 訂單單身
     *
     */
    static public function getOrderDetails($order_id)
    {
        return self::where('order_id', '=', $order_id)->get();
    }
}

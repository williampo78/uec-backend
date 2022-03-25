<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'order_details';
    protected $guarded = [];

    /**
     * 建立與訂單的關聯
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * 建立與商品的關聯
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * 建立與商品品項的關聯
     */
    public function productItem()
    {
        return $this->belongsTo(ProductItem::class, 'product_item_id');
    }

    /**
     * 建立與出貨單明細的關聯
     */
    public function shipmentDetail()
    {
        return $this->hasOne(ShipmentDetail::class, 'order_detail_id');
    }

    /**
     * 取得 訂單單身
     *
     */
    public static function getOrderDetails($order_id)
    {
        return self::where('order_id', '=', $order_id)->get();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $guarded = [];

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'order_no', 'order_no');
    }

    public function orderCampaignDiscounts()
    {
        return $this->hasMany(OrderCampaignDiscount::class, 'order_id');
    }

    /**
     * 取得 訂單
     *
     */
    static public function getOrder($order_id)
    {
        return self::where('id', '=', $order_id)->first();
    }
}

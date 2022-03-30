<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentDetail extends Model
{
    use HasFactory;

    protected $table = 'shipment_details';
    protected $guarded = [];

    /**
     * 建立與出貨單的關聯
     */
    public function shipment()
    {
        return $this->belongsTo(Shipment::class, 'shipment_id');
    }

    /**
     * 建立與訂單明細的關聯
     */
    public function orderDetail()
    {
        return $this->belongsTo(OrderDetail::class, 'order_detail_id');
    }
}

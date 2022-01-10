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
     * 取得 訂單單身
     *
     */
    static public function getOrderDetails($order_id)
    {
        return self::where('order_id', '=', $order_id)->get();
    }
}

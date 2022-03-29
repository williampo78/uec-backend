<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $guarded = [];

    /**
     * 建立與訂單明細的關聯
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'product_id');
    }

    /**
     * 建立與商品圖片的關聯
     */
    public function productPhotos()
    {
        return $this->hasMany(ProductPhoto::class, 'product_id');
    }
}

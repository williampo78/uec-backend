<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuotationDetail extends Model
{
    use HasFactory;
    protected $table = 'quotation_details';
    /**
     * 建立與商品的關聯
     *
     */
    // public function product()
    // {
    //     return $this->belongsTo(Product::class, 'product_id');
    // }
    public function productItem(){
        return $this->belongsTo(ProductItem::class,'product_item_id');
    }
}

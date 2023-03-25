<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;

    protected $table = 'purchase_detail';
    protected $guarded = [];  

    public function supplier(){
        return $this->belongsTo(Supplier::class,'supplier_id');
    }

    public function productItem(){
        return $this->belongsTo(ProductItem::class,'product_item_id') ;
    }

    public function warehouse(){
        return $this->belongsTo(Warehouse::class,'warehouse_id') ;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $table = 'purchase';
    protected $guarded = [];  

    public function purchaseDetail(){
        return $this->hasMany(PurchaseDetail::class, 'purchase_id');
    }

    public function orderSupplier(){
        return $this->belongsTo(OrderSupplier::class,'order_supplier_id');
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class,'supplier_id');
    }
    
    
}

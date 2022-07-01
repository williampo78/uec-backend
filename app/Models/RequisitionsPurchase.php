<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionsPurchase extends Model
{
    use HasFactory;
    protected $table = 'requisitions_purchase';

    public function orderSupplier()
    {
        return $this->hasOne(OrderSupplier::class, 'requisitions_purchase_id');
    }
    public function requisitionsPurchaseDetail(){
        return $this->hasMany(RequisitionsPurchaseDetail::class,'requisitions_purchase_id');
    }
}

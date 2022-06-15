<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionsPurchaseDetail extends Model
{
    use HasFactory;
    protected $table = 'requisitions_purchase_detail';

    public function requisitionsPurchase()
    {
        return $this->belongsTo(RequisitionsPurchase::class, 'requisitions_purchase_id');
    }
}

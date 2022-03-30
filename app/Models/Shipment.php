<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $table = 'shipments';
    protected $guarded = [];

    public function shipmentDetails()
    {
        return $this->hasMany(ShipmentDetail::class, 'shipment_id');
    }
}

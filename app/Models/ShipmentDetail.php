<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentDetail extends Model
{
    use HasFactory;

    protected $table = 'shipment_details';
    protected $guarded = [];
}

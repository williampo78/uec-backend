<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderCampaignDiscount extends Model
{
    use HasFactory;

    protected $table = 'order_campaign_discounts';
    protected $guarded = [];
}

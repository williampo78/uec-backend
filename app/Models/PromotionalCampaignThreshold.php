<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionalCampaignThreshold extends Model
{
    use HasFactory;

    protected $table = 'promotional_campaign_thresholds';
    protected $guarded = [];
}

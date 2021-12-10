<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionalCampaigns extends Model
{
    use HasFactory;

    protected $table = 'promotional_campaigns';
    protected $guarded = [];
}

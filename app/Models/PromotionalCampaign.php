<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionalCampaign extends Model
{
    use HasFactory;

    protected $table = 'promotional_campaigns';
    protected $guarded = [];

    /**
     * 滿額活動指定商品
     */
    public function campaignProduct()
    {
        return $this->hasMany(PromotionalCampaignProduct::class, 'promotional_campaign_id');
    }
}

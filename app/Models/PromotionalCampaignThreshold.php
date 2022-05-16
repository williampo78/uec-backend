<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionalCampaignThreshold extends Model
{
    use HasFactory;
    protected $table = 'promotional_campaign_thresholds';
    protected $guarded = [];

    /**
     * 建立與行銷活動的關聯
     */
    public function promotionalCampaign()
    {
        return $this->belongsTo(PromotionalCampaign::class, 'promotional_campaign_id');
    }

    /**
     * 建立與行銷活動-贈品or加購品的關聯
     */
    public function promotionalCampaignGiveaways()
    {
        return $this->hasMany(PromotionalCampaignGiveaway::class, 'threshold_id');
    }
}

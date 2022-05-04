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
     * 活動門檻的關聯
     */
    public function campaignThreshold()
    {
        return $this->hasMany(PromotionalCampaignThreshold::class, 'promotional_campaign_id')->orderBy('n_value');
    }
}

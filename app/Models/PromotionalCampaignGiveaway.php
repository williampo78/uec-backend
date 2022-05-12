<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionalCampaignGiveaway extends Model
{
    use HasFactory;

    protected $table = 'promotional_campaign_giveaways';
    protected $guarded = [];

    /**
     * 建立與商品的關聯
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * 建立與行銷活動-門檻的關聯
     */
    public function promotionalCampaignThreshold()
    {
        return $this->belongsTo(PromotionalCampaignThreshold::class, 'threshold_id');
    }
}

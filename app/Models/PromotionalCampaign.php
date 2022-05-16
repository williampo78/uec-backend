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

    /**
     * 活動門檻的關聯
     */
    public function campaignThreshold()
    {
        return $this->hasMany(PromotionalCampaignThreshold::class, 'promotional_campaign_id')->orderBy('n_value');
    }

    /**
     * 上下架狀態
     *
     * 待上架：狀態為【生效】，且【當下時間】小於【上架開始時間】
     * 已上架：狀態為【生效】，且【當下時間】介於【上架開始時間】與【上架結束時間】之間
     * 下架：狀態為【生效】，且【當下時間】大於【上架結束時間】
     * 關閉：狀態為【失效】
     *
     * @return string
     */
    public function getLaunchStatusAttribute()
    {
        $startAt = Carbon::parse($this->start_at);
        $endAt = Carbon::parse($this->end_at);
        $launchStatus = null;

        // 狀態為生效
        if ($this->active) {
            if (now()->lessThan($startAt)) {
                $launchStatus = '待上架';
            } elseif (now()->between($startAt, $endAt)) {
                $launchStatus = '已上架';
            } else {
                $launchStatus = '下架';
            }
        }
        // 狀態為失效
        else {
            $launchStatus = '關閉';
        }

        return $launchStatus;
    }

    /**
     * 建立與行銷類型設定檔的關聯
     */
    public function campaignType()
    {
        return $this->belongsTo(LookupValuesV::class, 'campaign_type', 'code')->where('type_code', 'CAMPAIGN_TYPE');
    }

    /**
     * 建立與行銷活動-主商品的關聯
     */
    public function promotionalCampaignProducts()
    {
        return $this->hasMany(PromotionalCampaignProduct::class, 'promotional_campaign_id');
    }

    /**
     * 建立與行銷活動-贈品or加購品的關聯
     */
    public function promotionalCampaignGiveaways()
    {
        return $this->hasMany(PromotionalCampaignGiveaway::class, 'promotional_campaign_id');
    }

    /**
     * 建立與行銷活動-門檻的關聯
     */
    public function promotionalCampaignThresholds()
    {
        return $this->hasMany(PromotionalCampaignThreshold::class, 'promotional_campaign_id');
    }
}

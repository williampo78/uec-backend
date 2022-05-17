<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderCampaignDiscount extends Model
{
    use HasFactory;

    protected $table = 'order_campaign_discounts';
    protected $guarded = [];

    /**
     * 建立與訂單的關聯
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * 建立與商品的關聯
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * 建立與商品品項的關聯
     */
    public function productItem()
    {
        return $this->belongsTo(ProductItem::class, 'product_item_id');
    }

    /**
     * 建立與行銷活動的關聯
     */
    public function promotionalCampaign()
    {
        return $this->belongsTo(PromotionalCampaign::class, 'promotion_campaign_id');
    }

    /**
     * 建立與活動門檻的關聯
     */
    public function promotionalCampaignThreshold()
    {
        return $this->belongsTo(PromotionalCampaignThreshold::class, 'campaign_threshold_id');
    }
}

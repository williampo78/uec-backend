<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderCampaignDiscount extends Model
{
    use HasFactory;

    protected $table = 'order_campaign_discounts';
    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function productItem()
    {
        return $this->belongsTo(ProductItem::class, 'product_item_id');
    }

    public function promotionalCampaign()
    {
        return $this->belongsTo(PromotionalCampaign::class, 'promotion_campaign_id');
    }
    public function promotionalCampaignThresholds()
    {
        return $this->belongsTo(PromotionalCampaignThreshold::class, 'campaign_threshold_id');
    }
}

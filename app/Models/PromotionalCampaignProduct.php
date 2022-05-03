<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionalCampaignProduct extends Model
{
    use HasFactory;

    protected $table = 'promotional_campaign_products';
    protected $guarded = [];

    /**
     * 建立與商品的關聯
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}

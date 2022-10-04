<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnOrderDetail extends Model
{
    use HasFactory;

    protected $table = 'return_order_details';
    protected $guarded = [];

    /**
     * 建立與訂單的關聯
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_no', 'order_no');
    }

    /**
     * 建立與商品品項的關聯
     */
    public function productItem()
    {
        return $this->belongsTo(ProductItem::class, 'product_item_id');
    }

    /**
     * 建立與退貨申請單的關聯
     */
    public function returnRequest()
    {
        return $this->belongsTo(ReturnRequest::class, 'return_request_id');
    }

    /**
     * 建立與行銷活動的關聯
     */
    public function promotionalCampaign()
    {
        return $this->belongsTo(PromotionalCampaign::class, 'promotional_campaign_id');
    }

    /**
     * 取得 退貨成功 排序
     */

    public function getPriorityAttribute()
    {
       $response = 0;
       if ($this->data_type == 'PRD') {
            $response = 1;
       } elseif ($this->data_type == 'CAMPAIGN') {
            $response = 2;
       } 
       return $response;
    }
    
}

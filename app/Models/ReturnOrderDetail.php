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

    /**
     * 取得 退貨成功 排序2
     */
    public function getDtlDescAttribute()
    {
        $response = 0;

        if ($this->data_type == 'PRD') {
            $temp = [];

            if (isset($this->productItem)) {
                $temp['item_no'] = $this->productItem->item_no;
                $temp['spec_1_value'] = $this->productItem->spec_1_value;
                $temp['spec_2_value'] = $this->productItem->spec_2_value;
            }

            if (isset($this->productItem->product)) {
                $temp['product_name'] = $this->productItem->product->product_name;
                $temp['spec_dimension'] = $this->productItem->product->spec_dimension;
            }

            if ($temp['spec_dimension'] == 0) {
                $response = $temp['item_no'] . '_' . $temp['product_name'];
            } elseif ($temp['spec_dimension'] == 1) {
                $response = $temp['item_no'] . '_' . $temp['product_name'] . '_' . $temp['spec_1_value'];
            } elseif ($temp['spec_dimension'] == 2) {
                $response = $temp['item_no'] . '_' . $temp['product_name'] . '_' . $temp['spec_1_value'] . '/' . $temp['spec_2_value'];
            }
        } elseif ($this->data_type == 'CAMPAIGN') {
            if (isset($this->promotionalCampaign)) {
                $response = $this->promotionalCampaign->campaign_brief;
            }
        }

        return $response;
    }
}

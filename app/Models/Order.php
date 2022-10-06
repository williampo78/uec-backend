<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';
    protected $guarded = [];

    /**
     * 建立與訂單明細的關聯
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    /**
     * 建立與出貨單的關聯
     */
    public function shipments()
    {
        return $this->hasMany(Shipment::class, 'order_no', 'order_no');
    }

    /**
     * 建立與訂單折扣的關聯
     */
    public function orderCampaignDiscounts()
    {
        return $this->hasMany(OrderCampaignDiscount::class, 'order_id');
    }

    /**
     * 建立與訂單金流單的關聯
     */
    public function orderPayments()
    {
        return $this->hasMany(OrderPayment::class, 'order_no', 'order_no');
    }

    /**
     * 建立與發票開立的關聯
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'order_no', 'order_no');
    }

    /**
     * 建立與發票折讓的關聯
     */
    public function invoiceAllowances()
    {
        return $this->hasMany(InvoiceAllowance::class, 'order_no', 'order_no');
    }

    /**
     * 建立與捐贈機構設定檔的關聯
     */
    public function donatedInstitution()
    {
        return $this->belongsTo(LookupValuesV::class, 'donated_institution', 'code')->where('type_code', 'DONATED_INSTITUTION');
    }

    /**
     * 建立與退貨申請單的關聯
     */
    public function returnRequests()
    {
        return $this->hasMany(ReturnRequest::class, 'order_no', 'order_no');
    }

    /**
     * 建立與銷退明細的關聯
     */
    public function returnOrderDetails()
    {
        return $this->hasMany(ReturnOrderDetail::class, 'order_no', 'order_no');
    }

    /**
     * 取得 訂單
     *
     */
    public static function getOrder($order_id)
    {
        return self::where('id', '=', $order_id)->first();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceAllowance extends Model
{
    use HasFactory;

    protected $table = 'invoice_allowance';
    protected $guarded = [];

    /**
     * 建立與訂單的關聯
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_no', 'order_no');
    }

    /**
     * 建立與發票折讓明細的關聯
     */
    public function invoiceAllowanceDetails()
    {
        return $this->hasMany(InvoiceAllowanceDetail::class, 'invoice_allowance_id');
    }

    /**
     * 建立與發票開立的關聯
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}

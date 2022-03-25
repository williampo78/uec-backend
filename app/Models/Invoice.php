<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';
    protected $guarded = [];

    /**
     * 建立與訂單的關聯
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_no', 'order_no');
    }

    /**
     * 建立與發票開立明細的關聯
     */
    public function invoiceDetails()
    {
        return $this->hasMany(InvoiceDetail::class, 'invoice_id');
    }

    /**
     * 建立與發票折讓的關聯
     */
    public function invoiceAllowances()
    {
        return $this->hasMany(InvoiceAllowance::class, 'invoice_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceAllowanceDetail extends Model
{
    use HasFactory;

    protected $table = 'invoice_allowance_details';
    protected $guarded = [];

    /**
     * 建立與發票折讓的關聯
     */
    public function invoiceAllowance()
    {
        return $this->belongsTo(InvoiceAllowance::class, 'invoice_allowance_id');
    }
}

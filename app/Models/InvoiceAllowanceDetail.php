<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceAllowanceDetail extends Model
{
    use HasFactory;

    protected $table = 'invoice_allowance_details';
    protected $guarded = [];
}

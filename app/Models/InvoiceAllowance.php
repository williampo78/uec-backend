<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceAllowance extends Model
{
    use HasFactory;

    protected $table = 'invoice_allowance';
    protected $guarded = [];
}

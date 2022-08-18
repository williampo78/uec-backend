<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderPaymentSecrets extends Model
{
    use HasFactory;
    protected $table = 'order_payment_secrets';
}

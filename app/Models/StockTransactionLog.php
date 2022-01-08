<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransactionLog extends Model
{
    use HasFactory;
    protected $table = 'stock_transaction_log';
    protected $guarded = [];
}

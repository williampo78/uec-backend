<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMonthlySummary extends Model
{
    use HasFactory;
    protected $table = 'stock_monthly_summary';
    protected $guarded = [];
}

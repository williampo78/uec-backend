<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMonthlySummary extends Model
{
    use HasFactory;
    protected $table = 'stock_monthly_summary';
    protected $guarded = [];


    /**
     * 取得 滾算年月-前月
     *
     */
    static public function getMonthData($month)
    {
        return self::where('transaction_month', '=', $month)->count();
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}

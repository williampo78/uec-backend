<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransactionLog extends Model
{
    use HasFactory;

    protected $table = 'stock_transaction_log';
    protected $guarded = [];

    /**
     * @return BelongsTo
     * @Author: Eric
     * @DateTime: 2022/7/28 下午 04:54
     */
    public function productItem():BelongsTo
    {
        return $this->belongsTo(ProductItem::class, 'product_item_id', 'id');
    }

    /**
     * @return BelongsTo
     * @Author: Eric
     * @DateTime: 2022/7/28 下午 04:54
     */
    public function warehouse():BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }
}

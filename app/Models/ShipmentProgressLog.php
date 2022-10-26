<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentProgressLog extends Model
{
    use HasFactory;

    protected $table = 'shipment_progress_log';
    protected $guarded = [];

    /**
     * 與出貨單的關聯
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class, 'shipment_id');
    }

    /**
     * 與供應商出貨配送回報狀態設定檔的關聯
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function supShipProgress(): BelongsTo
    {
        return $this->belongsTo(LookupValuesV::class, 'progress_code', 'code')
            ->where('type_code', 'SUP_SHIP_PROGRESS');
    }

    /**
     * 與記錄者的關聯
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function loggedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'logged_by');
    }
}

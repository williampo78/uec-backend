<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatchUploadLog extends Model
{
    use HasFactory;

    protected $table = 'batch_upload_log';

    protected $guarded = [];

    /**
     * 供應商的關聯
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}

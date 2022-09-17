<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReturnExamination extends Model
{
    use HasFactory;

    protected $table = 'return_examinations';
    protected $guarded = [];

    /*
     * 建立與退貨檢驗單明細的關聯
     */
    public function returnExaminationDetails():HasMany
    {
        return $this->hasMany(ReturnExaminationDetail::class, 'return_examination_id');
    }

    public function supplier():belongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function returnRequest():belongsTo
    {
        return $this->belongsTo(ReturnRequest::class, 'return_request_id');
    }
}

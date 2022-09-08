<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnExamination extends Model
{
    use HasFactory;

    protected $table = 'return_examinations';
    protected $guarded = [];

    /*
     * 建立與退貨檢驗單明細的關聯
     */
    public function returnExaminationDetails()
    {
        return $this->hasMany(ReturnExaminationDetail::class, 'return_examination_id');
    }
}

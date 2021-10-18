<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequisitionsPurchaseReviewLog extends Model
{
    use HasFactory;
    protected $table = 'requisitions_pur_review_log';
    public $timestamps = true;
}

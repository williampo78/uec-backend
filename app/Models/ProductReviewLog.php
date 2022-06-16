<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReviewLog extends Model
{
    use HasFactory;
    protected $table = 'product_review_log';
    protected $guarded = [];
}

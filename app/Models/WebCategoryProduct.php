<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebCategoryProduct extends Model
{
    use HasFactory;

    protected $table = 'web_category_products';
    protected $guarded = [];
}

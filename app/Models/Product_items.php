<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product_items extends Model
{
    use HasFactory;

    protected $table = 'product_items';
    protected $guarded = [];  

}

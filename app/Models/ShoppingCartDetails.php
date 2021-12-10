<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShoppingCartDetails extends Model
{
    use HasFactory;
    protected $table = 'shopping_cart_details';
    protected $guarded = [];
}

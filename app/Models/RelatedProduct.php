<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelatedProduct extends Model
{
    use HasFactory;
    protected $table = 'related_products';
    public $timestamps = true;
    protected $guarded = [];

    static public function getRelated($product_id)
    {
        return self::where('product_id', '=', $product_id)->get();
    }
}

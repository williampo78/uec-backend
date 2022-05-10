<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebCategoryHierarchy extends Model
{
    use HasFactory;

    protected $table = 'web_category_hierarchy';
    protected $guarded = [];

    /**
     * 建立與商品的關聯
     */
    public function webCategoryHierarchies()
    {
        return $this->belongsToMany(Product::class, 'web_category_products', 'web_category_hierarchy_id', 'product_id')->withTimestamps();
    }
}

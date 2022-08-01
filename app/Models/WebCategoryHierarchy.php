<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class WebCategoryHierarchy extends Model
{
    use HasFactory, NodeTrait;

    protected $table = 'web_category_hierarchy';
    protected $guarded = [];

    /**
     * 對應 lft column
     */
    public function getLftName()
    {
        return 'lft';
    }

    /**
     * 對應 rgt column
     */
    public function getRgtName()
    {
        return 'rgt';
    }

    /**
     * 建立與商品的關聯
     */
    public function products()
    {
        return $this->belongsToMany(Product::class, 'web_category_products', 'web_category_hierarchy_id', 'product_id')->withTimestamps();
    }
}

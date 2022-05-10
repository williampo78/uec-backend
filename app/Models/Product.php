<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';
    protected $guarded = [];

    /**
     * 上下架狀態
     *
     * @return string|null
     */
    public function getLaunchStatusAttribute()
    {
        $launchStatus = null;

        // 上架狀態
        switch ($this->approval_status) {
            case 'NA':
                $launchStatus = '未設定';
                break;

            case 'REVIEWING':
                $launchStatus = '上架申請';
                break;

            case 'REJECTED':
                $launchStatus = '上架駁回';
                break;

            case 'CANCELLED':
                $launchStatus = '商品下架';
                break;

            case 'APPROVED':
                $launchStatus = Carbon::now()->between($this->start_launched_at, $this->end_launched_at) ? '商品上架' : '商品下架';
                break;
        }

        return $launchStatus;
    }

    /**
     * 毛利
     *
     * @return int|null
     */
    public function getGrossMarginAttribute()
    {
        $product = self::select(
            DB::raw('get_latest_product_cost(id, TRUE) AS item_cost'),
        )->find($this->id);

        $sellingPrice = $this->getRawOriginal('selling_price');

        return (isset($product->item_cost, $sellingPrice) && $sellingPrice != 0) ? round(((1 - ($product->item_cost / $sellingPrice)) * 100), 2) : null;
    }

    /**
     * 建立與訂單明細的關聯
     */
    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'product_id');
    }

    /**
     * 建立與商品圖片的關聯
     */
    public function productPhotos()
    {
        return $this->hasMany(ProductPhoto::class, 'product_id');
    }

    /**
     * 建立與供應商的關聯
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * 建立與前台分類階層的關聯
     */
    public function webCategoryHierarchies()
    {
        return $this->belongsToMany(WebCategoryHierarchy::class, 'web_category_products', 'product_id', 'web_category_hierarchy_id')->withTimestamps();
    }
}

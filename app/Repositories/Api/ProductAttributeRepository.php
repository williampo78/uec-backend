<?php

namespace App\Repositories\Api;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;

class ProductAttributeRepository
{
    private $configLevels;

    public function __construct()
    {
        $this->configLevels = config('uec.web_category_hierarchy_levels');
    }

    /**
     *
     *
     * @return void
     */
    public function getAttributeFilter($params)
    {
        $params = $this->setParams($params);
        $result = Product::select('products.*')
            ->where('approval_status', 'APPROVED')
            ->where('start_launched_at', '<=', now())
            ->where('end_launched_at', '>=', now())
            ->where('product_type', 'N')
            ->whereHas('webCategoryHierarchies', function (Builder $query) use ($params) {
                return $query->where('active', '1');
            })
            ->with('productAttribute')
            ->join('web_category_products', 'products.id', '=', 'web_category_products.product_id')
            ->join('web_category_hierarchy as wch1', 'wch1.id', '=', 'web_category_products.web_category_hierarchy_id')
            ->join('web_category_hierarchy as wch2', 'wch2.id', '=', 'wch1.parent_id')
            ->when($this->configLevels == 3, function ($query) use ($params) {
                return $query->join('web_category_hierarchy as wch3', 'wch3.id', '=', 'wch2.parent_id');
            })
        ;
        //關鍵字搜尋
        if ($params['keyword']) {
            $result = $result->where(function ($query) use ($params) {
                $query->where('product_name', 'like', "%{$params['keyword']}%")
                    ->orWhere('product_no', 'like', "%{$params['keyword']}%")
                    ->orWhere('keywords', 'like', "%{$params['keyword']}%")
                    ->orWhere('wch1.category_name', 'like', "%{$params['keyword']}%")
                    ->orWhere('wch2.category_name', 'like', "%{$params['keyword']}%")
                    ->when($this->configLevels == 3, function ($query) use ($params) {
                        return $query->orWhere('wch3.category_name', 'like', "%{$params['keyword']}%");
                    })
                    ->orWhereHas('brand', function (Builder $query) use ($params) {
                        $query->where('brand_name', 'like', "%{$params['keyword']}%");
                    });
            });
        }
        //價格區間
        if ($params['price_min'] >= 0 && $params['price_max'] > 0) {
            $result = $result->whereBetween('selling_price', [$params['price_min'], $params['price_max']]);
        }
        //依照分類搜尋
        if ($params['category']) {
            $result = $result->whereHas('webCategoryHierarchies', function (Builder $query) use ($params) {
                $query = $query->where('web_category_hierarchy_id', $params['category']);
            });
        }
        //品牌搜尋
        if ($params['brand']) {
            $result = $result->whereHas('brand', function (Builder $query) use ($params) {
                $brand = explode(',', $params['brand']);
                $query = $query->whereIn('id', $brand);
            });
        }
        //適用族群
        if ($params['group']) {
            $group = explode(',', $params['group']);
            $result = $result->where(function ($query) use ($group) {
                $query = $query->whereHas('productAttribute', function (Builder $query) use ($group) {
                    $query->whereIn('product_attribute_lov_id', $group);
                });
            });
        }
        //成分
        if ($params['ingredient']) {
            $ingredient = explode(',', $params['ingredient']);
            $result = $result->where(function ($query) use ($ingredient) {
                $query = $query->whereHas('productAttribute', function (Builder $query) use ($ingredient) {
                    $query->whereIn('product_attribute_lov_id', $ingredient);
                });
            });
        }
        //認證
        if ($params['certificate']) {
            $certificate = explode(',', $params['certificate']);
            $result = $result->where(function ($query) use ($certificate) {
                $query = $query->whereHas('productAttribute', function (Builder $query) use ($certificate) {
                    $query->whereIn('product_attribute_lov_id', $certificate);
                });
            });
        }
        //劑型
        if($params['dosage_form']){
            $dosage_form = explode(',', $params['dosage_form']);
            $result = $result->where(function ($query) use ($dosage_form) {
                $query = $query->whereHas('productAttribute', function (Builder $query) use ($dosage_form) {
                    $query->whereIn('product_attribute_lov_id', $dosage_form);
                });
            });
        }

        $result = $result->get()->unique('id');

        $result = $result->map(function ($obj) {
            $obj->brand_name = $obj->brand->brand_name;
            return $obj;
        });
        return $result;
    }

    public function setParams($params)
    {
        return [
            'keyword' => $params->keyword ?? null, //關鍵字
            'category' => $params->category ?? null, //分類
            'group' => $params->group ?? null, //適用族群
            'ingredient' => $params->ingredient ?? null, //成分
            'dosage_form'=>$params->dosage_form ?? null, //劑型
            'brand' => $params->brand ?? null, //品牌
            'certificate' => $params->certificate ?? null, //認證
            'price_min' => $params->price_min ?? null, //最低價格
            'price_max' => $params->price_max ?? null, //最高價格
        ];
    }

}

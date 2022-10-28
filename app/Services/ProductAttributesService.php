<?php

namespace App\Services;

use App\Models\ProductAttribute;

class ProductAttributesService
{
     /**
     * 取得產品設定的屬性
     *
     * @param [array] $in
     * @return array
     */
    public function getProductAttributes($in)
    {
        $result = new ProductAttribute;
        if (isset($in['product_id']) && $in['product_id']) {
            $result = $result->where('product_id', $in['product_id']);
        }
        $result = $result->get()->keyBy('product_attribute_lov_id')->toArray();
        return $result;
    }
}

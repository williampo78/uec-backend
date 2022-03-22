<?php

namespace App\Services;

use App\Models\ProductAttribute;

class ProductAttributesService
{
    public function getProductAttributes($in)
    {
        $result = new ProductAttribute;
        if (isset($in['product_id']) && $in['product_id']) {
            $result = $result->where('product_id', $in['product_id']);
        }
        if (isset($in['attribute_type']) && $in['attribute_type']) {
            $result = $result->where('attribute_type', $in['attribute_type']);
        }
        $result = $result->get();
        return $result;
    }
}

<?php

namespace App\Services;

use App\Models\ProductAttributeLov;

class ProductAttributeLovService
{
    public function getProductAttributeLov($in)
    {
        $result = new ProductAttributeLov ;
        if (isset($in['attribute_type']) && $in['attribute_type'] !== '') {
            $result = $result->where('attribute_type', $in['attribute_type']);
        };
        $result = $result->get() ;

        return $result;
    }
}

<?php

namespace App\Services;

use App\Models\ProductAttributeLov;

class ProductAttributeLovService
{

    public function getProductAttributeLov($in)
    {
        $result = new ProductAttributeLov;
        if (isset($in['attribute_type']) && $in['attribute_type'] !== '') {
            $result = $result->where('attribute_type', $in['attribute_type']);
        };
        $result = $result->get();

        return $result;
    }

    /*
     * 取得進階搜尋欄位
     */
    public function getAttributeForSearch()
    {
        $condition = ['GROUP', 'INGREDIENT', 'CERTIFICATE'];
        $result = ProductAttributeLov::select('id', 'attribute_type', 'code', 'description')
            ->where('active', 1)
            ->whereIn('attribute_type', $condition)
            ->orderBy('sort', 'asc')
            ->orderBy('id', 'asc')
            ->get()
            ->toArray();
        return $result;
    }
}

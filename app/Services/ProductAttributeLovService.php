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
     * 進階搜尋商品欄位
     */
    public function getProductFilter()
    {
        $condition = ['GROUP', 'INGREDIENT', 'DOSAGE_FORM', 'CERTIFICATE'];
        $result = ProductAttributeLov::select('id', 'attribute_type', 'code', 'description')
            ->where('active', 1)
            ->whereIn('attribute_type', $condition)
            ->orderBy('sort', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        $filter = [];
        foreach ($result as $data) {
            $filter[$data->attribute_type][] = array(
                'id' => $data->id,
                'code' => $data->code,
                'name' => $data->description
            );
        }
        if (count($filter) > 0) {
            $result['status'] = 200;
            $result['result'] = $filter;
        } else {
            $result['status'] = 401;
            $result['result'] = null;
        }
        return $result;
    }
}

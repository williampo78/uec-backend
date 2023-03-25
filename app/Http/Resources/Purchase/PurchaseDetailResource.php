<?php

namespace App\Http\Resources\Purchase;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        $brand_name       = $this->productItem->product->brand->brand_name ?? '';
        $product_name     = $this->productItem->product->product_name ?? '';
        $combination_name = $brand_name . '-' . $product_name;

        if (!empty($this->productItem->product->spec_1_value)) {
            $combination_name .= '-' . $this->productItem->product->spec_1_value;
        }

        if (!empty($this->productItem->product->spec_2_value)) {
            $combination_name .= '-' . $this->productItem->product->spec_2_value;
        }

        return [
            'item_no'                 => $this->item_no,
            'pos_item_no'             => $this->productItem->pos_item_no ?? '',
            'brand_name'              => $brand_name,
            'combination_name'        => $combination_name,
            'expiry_date'             => $this->expiry_date,
            'warehouse_name'          => $this->warehouse->name,
            'item_price'              => $this->item_price,
            'item_qty'                => $this->item_qty,
            'original_subtotal_price' => $this->original_subtotal_price,
        ];
    }
}

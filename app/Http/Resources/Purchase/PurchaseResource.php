<?php

namespace App\Http\Resources\Purchase;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Purchase\PurchaseDetailResource;


class PurchaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id'                       => $this->id,
            'trade_date'               => $this->trade_date,
            'number'                   => $this->number,
            'order_supplier_number'    => $this->orderSupplier->number,
            'total_price'              => $this->total_price,
            'invoice_number'           => $this->invoice_number,
            'supplier_name'            => $this->supplier->name,
            'txa_name'                 => config('uec.tax_option')[$this->tax] ?? "",
            'currency_code'            => $this->currency_code,
            'original_total_tax_price' => $this->original_total_tax_price,
            'original_total_price'     => $this->original_total_price,
            'total_tax_price'          => $this->total_tax_price,
            'invoice_address'          => $this->invoice_address,
            'invoice_date'             => $this->invoice_date,
            'remark'                   => $this->remark,
            'purchase_detail'          => PurchaseDetailResource::collection($this->purchaseDetail) ,
        ];
    }
}

<?php

namespace App\Http\Resources\Supplier\Index;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
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
            'id' => $this->id,
            'display_number' => $this->display_number,
            'company_number' => $this->company_number,
            'short_name' => $this->short_name,
            'name' => $this->name,
            'active_name' => $this->active_name,
            'telephone' => $this->telephone,
            'address' => $this->address,
            $this->mergeWhen($this->relationLoaded('paymentTerm'), [
                'payment_term_name' => $this->paymentTerm->description ?? null,
            ]),
        ];
    }
}

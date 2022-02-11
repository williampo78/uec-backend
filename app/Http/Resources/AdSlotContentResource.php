<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdSlotContentResource extends JsonResource
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
            'slot_code' => $this->slot_code,
            'slot_desc' => $this->slot_desc,
            'is_mobile_applicable' => $this->is_mobile_applicable,
            'is_desktop_applicable' => $this->is_desktop_applicable,
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'slot_content_id' => $this->slot_content_id,
            'description' => $this->description,
        ];
    }
}

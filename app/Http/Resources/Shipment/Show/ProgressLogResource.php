<?php

namespace App\Http\Resources\Shipment\Show;

use App\Http\Resources\Shipment\Show\ShipmentDetailResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class ProgressLogResource extends JsonResource
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
            'logged_at' => $this->logged_at,
            $this->mergeWhen($this->relationLoaded('supShipProgress'), [
                'progress_code_name' => $this->supShipProgress->description,
            ]),
            'memo' => $this->memo,
            'agreed_date' => $this->agreed_date,
            $this->mergeWhen($this->relationLoaded('loggedBy'), [
                'logged_by' => $this->loggedBy->user_name ?? '系統排程',
            ]),
        ];
    }
}

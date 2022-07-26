<?php

namespace App\Http\Resources\WebCategoryHierarchy;

use Illuminate\Http\Resources\Json\JsonResource;

class WebCategoryHierarchyResource extends JsonResource
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
            'category_name' => $this->category_name,
            'category_level' => $this->category_level,
            'gross_margin_threshold' => $this->gross_margin_threshold * 100 / 100,
            'category_short_name' => $this->category_short_name,
            'icon_name' => !empty($this->icon_name) ? config('filesystems.disks.s3.url') . $this->icon_name : null,
        ];
    }
}

<?php

namespace App\Http\Resources\Advertisement\Launch;

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
            'children' => WebCategoryHierarchyResource::collection($this->whenLoaded('children')),
        ];
    }
}

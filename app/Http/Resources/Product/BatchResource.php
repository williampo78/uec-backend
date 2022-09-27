<?php

namespace App\Http\Resources\Product;

use App\Enums\BatchUploadLogStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class BatchResource extends JsonResource
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
            'excel_name'    =>  $this->source_file_1_name,
            'zip_name'      =>  $this->source_file_2_name,
            'status_name'   =>  BatchUploadLogStatus::STATUS_ARR[$this->status] ?? '',
            'created_at'    =>  $this->created_at->format("Y-m-d h:i"),
            'job_completed_at'  =>  $this->job_completed_at ?? "---",
            'job_completed_log'=>$this->job_completed_log,
            'job_log_file'=>$this->job_log_file,
        ];
    }
}

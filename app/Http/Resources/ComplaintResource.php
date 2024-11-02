<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComplaintResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'user_id'           => $this->user_id,
            'complaint_by'      => data_get($this,'user.name'),
            'title'             => $this->title,
            'priority'          => $this->priority,
            'description'       => $this->description,
            'attachment'        => $this->attachment,
            'complaint_at'      => date_format(date_create($this->created_at), 'd F Y'),
            'category'          => data_get($this,'category.name'),
            'category_id'       => $this->category_id,
            'status'            => $this->status,
            'comments'          => data_get($this,'comments')
           
           
        ];
    }
}

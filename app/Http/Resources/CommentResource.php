<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'commented_by'      => data_get($this,'commentby.name'),
            'comment'           => $this->comment,
            'complaint_id'      => $this->complaint_id,
            'comment_at'      => date_format(date_create($this->created_at), 'd F Y'),
           
           
        ];
    }
}

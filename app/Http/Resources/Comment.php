<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Comment extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'enable' => $this->enable,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'content' => $this->content,
            'like' => $this->like,
            'dislike' => $this->dislike,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'children' => Comment::collection($this->children)
        ];
    }
}

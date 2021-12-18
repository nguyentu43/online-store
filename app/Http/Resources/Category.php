<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Product;

class Category extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'order' => $this->order,
            'parent_id' => $this->parent_id,
            'image' => $this->image,
            'url' => empty($this->image) ? null : cloudinary()->getImage($this->image)->toUrl(),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'children' => Category::collection($this->children)
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Product as ProductResource;
use App\Product;
use Illuminate\Support\Facades\DB;

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
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'children' => Category::collection($this->children)
        ];
    }
}

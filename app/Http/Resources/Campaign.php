<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Product;

class Campaign extends JsonResource
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
            'name' => $this->name,
            'enable' => $this->enable ? true: false,
            'slug' => $this->slug,
            'description' => $this->description,
            'banner' => $this->banner,
            'url' => empty($this->banner) ? null : cloudinary()->getImage($this->banner)->toUrl(),
            'categories' => $this->categories ? $this->categories : [],
            'start_datetime' => $this->start_datetime ? $this->start_datetime->toDateTimeString() : null,
            'end_datetime' => $this->end_datetime ? $this->end_datetime->toDateTimeString() : null,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'products' => Product::collection($this->products)
        ];
    }
}

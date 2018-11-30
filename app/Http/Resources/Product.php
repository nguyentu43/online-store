<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProductSku;

class Product extends JsonResource
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
            "id" => $this->id,
            "name" => $this->name,
            "weight" => $this->weight,
            "description" => $this->description,
            "brand" => $this->brand,
            "enable" => $this->enable,
            "product_type" => $this->type,
            "category" => $this->category,
            "skus" => ProductSku::collection($this->skus),
            "attributes" => $this->attributes->sortBy('id')->values()->all(),
            "slug" => $this->slug,
            "rate" => $this->rate,
            "comment_count" => $this->comments->count(),
            "created_at" => $this->created_at->toDateTimeString(),
            "updated_at"=> $this->created_at->toDateTimeString()
        ];
    }
}

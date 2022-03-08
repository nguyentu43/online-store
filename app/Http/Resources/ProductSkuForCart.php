<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductSkuForCart extends JsonResource
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
            'sku' => $this->sku,
            'price' => $this->price,
            'images' => $this->images,
            'urls' => count($this->images) == 0 ? [] : array_map(function($item){
                return cloudinary()->getImage($item)->toUrl();
            }, $this->images),
            'discount' => $this->discount,
            'quantity' => $this->quantity,
            'name' => $this->name,
            'product' => $this->product,
            'pivot' => $this->pivot,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at'=> $this->created_at->toDateTimeString()
        ];
    }
}

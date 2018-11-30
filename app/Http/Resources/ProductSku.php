<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductSku extends JsonResource
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
            'media' => $this->media_list,
            'discount' => $this->discount,
            'quantity' => $this->quantity,
            'name' => $this->name,
            'promotion' => $this->promotion,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at'=> $this->created_at->toDateTimeString()
        ];
    }
}

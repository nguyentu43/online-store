<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Comment;

class Rate extends JsonResource
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
            'product_sku_id' => $this->product_sku_id,
            'sku_name' => $this->product_sku->name,
            'enable' => $this->enable,
            'user_name' => $this->user->name,
            'order_id' => $this->order_id,
            'rate' => $this->rate,
            'images' => $this->images,
            'comment' => new Comment($this->comments->first()),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString()
        ];
    }
}

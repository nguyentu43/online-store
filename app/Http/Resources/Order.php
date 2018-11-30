<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProductSkuForCart;
use Cartalyst\Stripe\Laravel\Facades\Stripe;
use App\Http\Resources\Rate as RateResource;

class Order extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {

        $stripe = Stripe::make('sk_test_BIoyl7l92wVmRZREi1GycsAH');
        //$stripe = Stripe::make(env('STRIPE_SECRET'));

        return [
            'id' => $this->id,
            'user' => $this->user,
            'name' => $this->name,
            'amount' => $this->amount,
            'address' => $this->address,
            'city' => $this->city,
            'description' => $this->description,
            'order_code' => $this->order_code,
            'phone' => $this->phone,
            'items' => ProductSkuForCart::collection($this->items),
            'payment_method' => $this->payment,
            'charge' => $this->charge_id ? $stripe->charges()->find($this->charge_id) : null,
            'status' => $this->status,
            'rate_list' => RateResource::collection($this->rate_list),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at'=> $this->created_at->toDateTimeString(),
            'discount' => $this->discount
        ];
    }
}

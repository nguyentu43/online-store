<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
    	'name', 'address', 'city', 'amount', 'description', 'phone', 'charge_id', 'order_code', 'discount'
    ];

    protected $casts = [
        'discount' => 'array'
    ];

    public function items()
    {
    	return $this->belongsToMany('App\ProductSku', 'order_detail', 'order_id', 'sku_id')->withPivot('quantity', 'price', 'discount');
    }

    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    public function status()
    {
        return $this->belongsToMany('App\OrderStatus', 'orders_order_status', 'order_id', 'status_id')->withTimestamps();
    }

    public function payment()
    {
        return $this->belongsTo('App\PaymentMethod', 'payment_method_id');
    }

    public function rate_list()
    {
        return $this->hasMany('App\Rate');
    }
}

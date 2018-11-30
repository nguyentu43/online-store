<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $table = "rate";

    protected $fillable = ['product_sku_id', 'rate', 'user_id', 'order_id', 'enable', 'images'];

    protected $casts = [
        'enable' => 'boolean',
        'images' => 'array'
    ];

    public function comments()
    {
    	return $this->morphMany('App\Comment', 'commentable');
    }

    public function order()
    {
        return $this->belongsTo('App\Order');
    }

    public function user()
    {
    	return $this->belongsTo('App\User');
    }

    public function product_sku()
    {
    	return $this->belongsTo('App\ProductSku');
    }
}

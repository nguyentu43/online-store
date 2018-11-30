<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    public function products()
    {
    	return $this->belongsToMany('App\ProductSku', 'cart_detail')->withPivot('quantity')->withTimestamps();
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    protected $fillable = ['name'];

    public function attributes(){
    	return $this->belongsToMany('App\ProductAttribute', 'product_attr_product_types');
    }

    public function products(){
    	return $this->hasMany('App\Product');
    }
}

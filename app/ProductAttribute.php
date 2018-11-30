<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    protected $fillable = ['name', 'filterable'];

    public function types()
    {
    	return $this->belongsToMany('App\ProductType');
    }

    public function products()
    {
    	return $this->belongsToMany('App\Product', 'product_product_attributes')->withPivot('value');
    }
}

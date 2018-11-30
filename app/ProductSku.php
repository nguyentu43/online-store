<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductSku extends Model
{
    protected $fillable = ['sku', 'quantity', 'price', 'name', 'promotion'];

    public function getPromotionAttribute()
    {
        $promotion = $this->attributes['promotion'];
        return strip_tags($promotion) == "" ? null : $promotion;
    }

    public function product()
    {
    	return $this->belongsTo('App\Product');
    }

    public function media_list()
    {
    	return $this->hasMany('App\Media');
    }

    public function discount()
    {
    	return $this->hasOne('App\Discount');
    }

    public function rate_list()
    {
        return $this->hasMany('App\Rate');
    }
}

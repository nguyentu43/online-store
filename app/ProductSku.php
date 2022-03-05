<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductSku extends Model
{
    protected $fillable = ['sku', 'quantity', 'price', 'name', 'promotion', 'images'];

    public function getPromotionAttribute()
    {
        $promotion = $this->attributes['promotion'];
        return strip_tags($promotion) == "" ? null : $promotion;
    }

    public function product()
    {
    	return $this->belongsTo('App\Product');
    }

    public function discount()
    {
    	return $this->hasOne('App\Discount');
    }

    public function rate_list()
    {
        return $this->hasMany('App\Rate');
    }

    public function getImagesAttribute()
    {
        return empty($this->attributes['images']) ? []: explode(';', $this->attributes['images']);
    }

    public function setImagesAttribute($arr)
    {
        $this->attributes['images'] = implode(';',$arr);
    }
}

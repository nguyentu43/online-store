<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\{Sluggable, SluggableScopeHelpers};
use Laravel\Scout\Searchable;

class Product extends Model
{
    use Sluggable, SluggableScopeHelpers, Searchable;

    protected $fillable = ['name', 'weight', 'description', 'brand_id', 
                            'product_type_id', 'category_id', 'enable'];

    public $casts = [
        'enable' => 'boolean'
    ];

    public function getDescriptionAttribute()
    {
        $desc = $this->attributes['description'];
        return strip_tags($desc) == "" ? null : $desc;
    }

    public function getRateAttribute()
    {
        $skus = $this->skus;
        return collect($skus->map(function($item){ return $item->rate_list->avg('rate'); }))->avg();
    }

    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function skus()
    {
    	return $this->hasMany('App\ProductSku');
    }

    public function attributes()
    {
    	return $this->belongsToMany('App\ProductAttribute', 'product_product_attributes')->withPivot('value');
    }

    public function category()
    {
    	return $this->belongsTo('App\Category');
    }

    public function type()
    {
    	return $this->belongsTo('App\ProductType', 'product_type_id');
    }

    public function brand()
    {
        return $this->belongsTo('App\Brand');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function comments()
    {
        return $this->morphMany('App\Comment', 'commentable');
    }

    public function toSearchableArray()
    {
        $array = $this->load(['skus', 'type', 'brand', 'attributes', 'category'])->toArray();

        unset($array['created_at']);
        unset($array['updated_at']);
        unset($array['enable']);
        unset($array['description']);
        unset($array['brand_id']);
        unset($array['product_type_id']);
        unset($array['category_id']);

        $array['skus'] = array_map(function($item){

            return [
                'name' => $item['name'],
                'price' => $item['price'],
                'sku' => $item['sku']
            ];

        }, $array['skus']);

        $array['attributes'] = array_map(function($item){

            return $item['name'].' '.$item['pivot']['value'];

        }, $array['attributes']);

        $array['brand'] = [
            'name' => $array['brand']['name']
        ];

        $array['type'] = [
            'name' => $array['type']['name']
        ];

        $array['category'] = [
            'name' => $array['category']['name']
        ];

        return $array;
    }
}

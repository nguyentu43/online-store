<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\{Sluggable, SluggableScopeHelpers};

class Product extends Model
{
    use Sluggable, SluggableScopeHelpers;

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

    protected function fullTextWildcards($term)
    {
        $reservedSymbols = ['-', '+', '<', '>', '@', '(', ')', '~'];
        $term = str_replace($reservedSymbols, '', $term);
        return $term;
    }

    public function scopeSearch($query, $term)
    {
        $columns = implode( ',', $this->searchable);
        $query->whereRaw("ts @@ tquery('english', ?)", $this->fullTextWildcards($term));
        return $query;
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Category extends Model
{
	use Sluggable;

    protected $fillable = ['name', 'order', 'parent_id', 'image', 'enable'];
    protected $dates = ['created_at', 'updated_at'];
    protected $casts = [ 'enable' => 'boolean' ];

    public function getParentOrderAttribute()
    {
        return explode('-', $this->attributes['order']);
    }

    public function sluggable()
    {
    	return [
    		'slug' => [
    			'source' => 'name'
    		]
    	];
    }

    public function parent()
    {
    	return $this->belongsTo('App\Category', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany('App\Category', 'parent_id');
    }

    public function products()
    {
        return $this->hasMany('App\Product');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function getOrderPathAttribute(){
        $array = explode(',', $this->order);
        array_push($array, $this->id);
        return array_map(function($item){ return intval($item);  },  $array);
    }
}

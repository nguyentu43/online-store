<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\{Sluggable, SluggableScopeHelpers};

class Campaign extends Model
{
	use Sluggable, SluggableScopeHelpers;

    protected $fillable = ['name', 'description', 'banner', 'video', 'start_datetime', 'end_datetime', 'enable', 'categories', 'slug'];

    protected $dates = ['start_datetime', 'end_datetime', 'created_at', 'updated_at'];

    protected $casts = [
        'enable' => 'boolean',
        'categories' => 'array'
    ];

    public function getDescriptionAttribute()
    {
        $desc = $this->attributes['description'];
        return strip_tags($desc) == "" ? null : $desc;
    }

    public function sluggable()
    {
    	return [
    		'slug' => [
    			'source' => 'name'
    		]
    	];
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function products()
    {
    	return $this->belongsToMany('App\Product', 'campaign_product');
    }

    public function comments()
    {
        return $this->morphMany('App\Comment', 'commentable');
    }
}

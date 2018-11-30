<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Brand extends Model
{
	use Sluggable;

    protected $fillable = ['name', 'description', 'image'];

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
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
	protected $fillable = ['content', 'user_id', 'parent_id', 'dislike', 'like', 'enable'];
    protected $casts = [
        'enable' => 'boolean'
    ];

    public function commentable()
    {
    	return $this->morphTo();
    }

    public function children()
    {
    	return $this->hasMany('App\Comment', 'parent_id');
    }

    public function user()
    {
    	return $this->belongsTo('App\User');
    }
}

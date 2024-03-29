<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'permission'];

    protected $casts = [
    	'permission' => 'array'
    ];
}

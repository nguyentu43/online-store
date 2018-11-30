<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoginProvider extends Model
{
    protected $fillable = ['name'];

    public function users()
    {
    	return $this->belongsToMany('App\User', 'login_providers_users');
    }
}

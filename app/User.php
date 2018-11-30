<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'gender', 'date_of_birth', 'enable'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];

    public $casts = [
        'enable' => 'boolean'
    ];

    public function roles()
    {
        return $this->belongsToMany('App\Role', 'roles_users');
    }

    public function cart()
    {
        return $this->hasOne('App\Cart');
    }

    public function address_books()
    {
        return $this->hasMany('App\AddressBook');
    }

    public function orders()
    {
        return $this->hasMany('App\Order');
    }

    public function password_reset()
    {
        return $this->hasOne('App\PasswordReset', 'email', 'email');
    }

    public function getRouteKeyName()
    {
        return 'email';
    }

    public function login_providers()
    {
        return $this->belongsToMany('App\LoginProvider', 'login_providers_users');
    }

    public function getPermissionAttribute()
    {
        $roles = $this->roles;

        $permission = [];

        foreach ($roles as $role) {
            $permission = array_merge($permission, $role->permission);
        }

        return $permission;
    }
}

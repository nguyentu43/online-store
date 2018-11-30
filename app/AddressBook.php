<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AddressBook extends Model
{
    protected $fillable = [
    	'name', 'address', 'city', 'phone'
    ];
}

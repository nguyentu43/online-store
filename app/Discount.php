<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Discount extends Model
{
    protected $fillable = ['value', 'start_datetime', 'end_datetime'];
    protected $dates = ['start_datetime', 'end_datetime'];

    public function getIsExpireAttribute()
    {
    	$now = Carbon::now();
    	$start = $this->start_datetime;
    	$end = $this->end_datetime;
    	return $now <= $end && $now >= $start;
    }
}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    //

    public function publisher()
    {
        return $this->belongsTo('App\Models\Publisher','publisher_id','id');
    }

    // trip has many resource
    public function resources()
    {
        return $this->hasMany('App\Models\TripResource','trip_id','id');
    }
    /**
     * Get the the trip in larave and casting to carbon date
     *
     * @param  string  $value
     * @return carbon date
     */
    public function getEndedAtAttribute($value)
    {
        return is_null($value)?" ":Carbon::parse($value);

    }
}

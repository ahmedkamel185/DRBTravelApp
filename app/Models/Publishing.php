<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Publishing extends Model
{
    //publisher
    public function publisher()
    {
        return $this->belongsTo('App\Models\Publisher','publisher_id','id');
    }

    // sharer
    public  function  sharer()
    {
        return $this->belongsTo('App\Models\Publisher','sharer_id','id');
    }

    // trip
    public  function  trip()
    {
        return $this->belongsTo('App\Models\Trip','trip_id','id');
    }

    // publish trip
    static function publishTrip($trip_id)
    {
        return Self::where('trip_id', $trip_id)->whereNull('sharer_id');
    }

    // share trip
    static function shareTrips($trip_id)
    {
        return Self::where('trip_id', $trip_id)->whereNotNull('sharer_id');
    }

    // likes
    public function likes()
    {
        return $this->hasMany('App\Models\Like','publishing_id','id');
    }
    public function favourits()
    {
        return $this->hasMany('App\Models\Favourit','publishing_id','id');
    }

    // likes
    public function comments()
    {
        return $this->hasMany('App\Models\Comment','publishing_id','id');
    }
}

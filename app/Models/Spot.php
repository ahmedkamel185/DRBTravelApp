<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spot extends Model
{
    protected $guarded = [];

    public function publisher()
    {
        return $this->belongsTo('App\Models\Publisher');
    }

    public function follower()
    {
        return $this->belongsTo('App\Models\Follower');
    }
    public function journal()
    {
        return $this->belongsTo("App\Models\Journal");
    }

    public function tags()
    {
        return $this->belongsToMany("App\Models\Tag");
    }

    public function files()
    {
        return $this->hasMany("App\Models\File");
    }

    public function likes()
    {
        return $this->hasMany("App\Models\Like", "spot_id");
    }

    public function comments()
    {
        return $this->hasMany("App\Models\Comment", "spot_id");
    }

    public function favourites()
    {
        return $this->hasMany("App\Models\Favourite", "spot_id");
    }

    public function country()
    {
        return $this->belongsTo('App\Models\Country');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\City');
    }

    public function subcity()
    {
        return $this->belongsTo('App\Models\Subcity');
    }

    public function locality()
    {
        return $this->belongsTo('App\Models\Locality');
    }

}

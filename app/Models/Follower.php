<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    //
    protected $fillable = [
        'follow_id', 'follower_id',
    ];
    // follow
    public  function follow()
    {
        return $this->belongsTo('App\Models\Publisher','follow_id','id');
    }

    // follower
    public  function follower()
    {
        return $this->belongsTo('App\Models\Publisher','follower_id','id');
    }

    // spots
    public function spots()
    {
        return $this->hasMany("App\Models\Spot", "publisher_id");
    }

    // get blocked id for user
    static function buldFollowerId($follow_id)
    {
        return Self::where('follow_id', $follow_id)->pluck('follower_id')->toArray();
    }
    static function buldFolloweId($follower_id)
    {
        return Self::where('follower_id', $follower_id)->pluck('follow_id')->toArray();
    }
}

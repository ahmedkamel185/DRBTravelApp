<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    // user give the block
    public  function user()
    {
        return $this->belongsTo('App\Models\Publisher','user_id','id');
    }

    // user who blocked
    public  function publisher()
    {
        return $this->belongsTo('App\Models\Publisher','publisher_id','id');
    }

    // get blocked id for user
    static function buldBlockId($user_id)
    {
        return Self::where('user_id', $user_id)->pluck('publisher_id')->toArray();
    }

    // get the id of perole which bllocked ueser

    static function buldBlockerId($publisher_id)
    {
        return Self::where('publisher_id', $publisher_id)->pluck('user_id')->toArray();
    }
}

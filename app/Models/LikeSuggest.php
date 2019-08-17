<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LikeSuggest extends Model
{
    //
    // benlong to publishing
    public  function suggest()
    {
        return $this->belongsTo('App\Models\Suggest','suggest_id','id');
    }

    // benlong to user
    public  function user()
    {
        return $this->belongsTo('App\Models\Publisher','user_id','id');
    }
}

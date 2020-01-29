<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Suggest extends Model
{
    //
    // likes
    public function likes()
    {
        return $this->hasMany('App\Models\LikeSuggest','suggest_id','id');
    }

    // likes
    public function comments()
    {
        return $this->hasMany('App\Models\CommentSuggest','suggest_id','id');
    }

    // benlong to user
    public  function publisher()
    {
        return $this->belongsTo('App\Models\Publisher','user_id','id');
    }
}

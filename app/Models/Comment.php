<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    // benlong to publishing
    public  function publishing()
    {
        return $this->belongsTo('App\Models\Publishing','publishing_id','id');
    }

    // benlong to user
    public  function user()
    {
        return $this->belongsTo('App\Models\Publisher','user_id','id');
    }
}

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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripResource extends Model
{
    // belong to trips
    public function trip()
    {
        return $this->belongsTo('App\Models\Trip','trip_id','id');
    }
}

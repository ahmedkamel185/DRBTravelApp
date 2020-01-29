<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{

    protected $guarded = [];
    // benlong to user
    public  function user()
    {
        return $this->belongsTo('App\Models\Publisher','publisher_id');
    }

    public function spot()
    {
        return $this->belongsTo("App\Models\Spot", "spot_id");
    }
}

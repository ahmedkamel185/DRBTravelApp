<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorePlace extends Model
{
    //
    public function store()
    {
        return $this->belongsTo('App\Models\Store','store_id','id');
    }
}

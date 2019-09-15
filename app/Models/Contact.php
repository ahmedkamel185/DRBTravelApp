<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    //
    public function publisher()
    {
        return $this->belongsTo('App\Models\Publisher','publisher_id','id');
    }
}

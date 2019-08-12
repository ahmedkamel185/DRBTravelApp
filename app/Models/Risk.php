<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Risk extends Model
{
    //
    public function publisher()
    {
        return $this->belongsTo('App\Models\Publisher','publisher_id','id');
    }

    public function risksComment()
    {
        return $this->hasMany('App\Models\RiskComment','risk_id','id');
    }
}

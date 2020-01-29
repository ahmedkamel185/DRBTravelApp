<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskComment extends Model
{
    //
    public function publisher()
    {
        return $this->belongsTo('App\Models\Publisher','publisher_id','id');
    }

    public function risk()
    {
        return $this->belongsTo('App\Models\Risk','risk_id','id');
    }
}

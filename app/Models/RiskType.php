<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskType extends Model
{
    //
    protected $table = "risk_types";
    public function risks()
    {
        return $this->hasMany('App\Models\Risk','risk_type_id','id');
    }
}

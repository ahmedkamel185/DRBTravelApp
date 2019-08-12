<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class StoreType extends Authenticatable
{
    //stores owner which register in the type
    public function stores()
    {
        return $this->hasMany('App\Models\Store','store_type_id','id');
    }
}

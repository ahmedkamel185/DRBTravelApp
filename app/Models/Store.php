<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Store extends Authenticatable
{
    use Notifiable;
    //stores ownener type

    public function StoreType()
    {
        return $this->belongsTo('App\Models\StoreType','store_type_id','id');
    }

    public function storePlaces()
    {
        return $this->hasMany('App\Models\StorePlace','store_id','id');
    }

}

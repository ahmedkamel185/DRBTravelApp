<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Publisher extends Authenticatable
{
    //
    protected $fillable = [
        'username', 'email', 'password','mobile','device_id',
    ];

    public function trips()
    {
        return $this->hasMany('App\Models\Trip','publisher_id','id');
    }

    public function risks()
    {
        return $this->hasMany('App\Models\Risk','risk_id','id');
    }

    public function risksComment()
    {
        return $this->hasMany('App\Models\RiskComment','risk_id','id');
    }

//    publishing
    public function publisings()
    {
        return $this->hasMany('App\Models\Publishing','publisher_id','id');
    }

    // sharing

    public function shares()
    {
        return $this->hasMany('App\Models\Publishing','sharer_id','id');
    }
}

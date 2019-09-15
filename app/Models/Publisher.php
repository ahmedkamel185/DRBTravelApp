<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Publisher extends Authenticatable
{
    //
    use Notifiable;
    protected $fillable = [
        'username', 'email', 'password','mobile','device_id',
    ];

    public function trips()
    {
        return $this->hasMany('App\Models\Trip','publisher_id','id');
    }

    public function risks()
    {
        return $this->hasMany('App\Models\Risk','publisher_id','id');
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

    // sharing
    public function logs()
    {
        return $this->hasMany('App\Models\LogActivity','publisher_id','id');
    }

    //likes
    public function likes()
    {
        return $this->hasMany('App\Models\Like','user_id','id');
    }

    //likes
    public function comments()
    {
        return $this->hasMany('App\Models\Comment','user_id','id');
    }
 // suggest
    public function suggest()
    {
        return $this->hasMany('App\Models\Suggest','user_id','id');
    }

    /*======================*/
    //likes
    public function suggestLikes()
    {
        return $this->hasMany('App\Models\LikeSuggest','user_id','id');
    }

    //likes
    public function suggestComments()
    {
        return $this->hasMany('App\Models\CommentSuggest','user_id','id');
    }

    /*======================*/
    // favourtits

    public function favourits()
    {
        return $this->hasMany('App\Models\Favourit','user_id','id');
    }

    // follows
    public function follows()
    {
        return $this->hasMany('App\Models\Follower','follow_id','id');
    }

    // follows
    public function followers()
    {
        return $this->hasMany('App\Models\Follower','follower_id','id');
    }

    // given block
    public function blockeds()
    {
        return $this->hasMany('App\Models\Block','publisher_id','id');
    }

    // user whiched blocked med
    public function blockers()
    {
        return $this->hasMany('App\Models\Block','user_id','id');
    }

    // user whic make blocked many
    public function blockerManys()
    {
        return $this->belongsToMany
        (
            'App\Models\Publisher',
            'blocks',
            'publisher_id',
            'user_id'
        )->withTimestamps();
    }

    // user which blocked me
    public function blockersManys()
    {
        return $this->belongsToMany
        (
            'App\Models\Publisher',
            'blocks',
            'user_id',
            'publisher_id'
        )->withTimestamps();
    }

    public function contacts()
    {
        return $this->hasMany('App\Models\Contact','publisher_id','id');
    }

}

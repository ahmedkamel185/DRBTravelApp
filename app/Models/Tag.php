<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $guarded = [];

    public function spots()
    {
        return $this->belongsToMany("App\Models\Spot");
    }

    public function scopeSearched($query)
    {
        $search = request()->input("tag");

        return Tag::where('name', 'LIKE' ,'%' . $search .'%')->take(5);
    }
}

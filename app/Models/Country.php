<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $guarded = [];

    public function spots()
    {
        return $this->hasMany("App\Models\Spot");
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    protected $guarded = [];

    public function spots()
    {
        return $this->hasMany("App\Models\Spot");
    }
}

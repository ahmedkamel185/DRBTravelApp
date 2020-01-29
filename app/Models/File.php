<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $guarded = [];

    public function spot()
    {
        return $this->belongsTo("App\Models\Spot");
    }
}

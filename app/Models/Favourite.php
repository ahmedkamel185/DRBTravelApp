<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favourite extends Model
{
    protected $guarded = [];

    public function spot()
    {
        return $this->belongsTo("App\Models\Spot", "spot_id");
    }

    public function user()
    {
        return $this->belongsTo("App\Models\Publisher", "publisher_id");
    }

    static function buldPublisherId($publisher_id)
    {
        return Self::where('publisher_id', $publisher_id)->pluck('spot_id')->toArray();
    }

}

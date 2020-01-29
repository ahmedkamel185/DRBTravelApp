<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GetCitiesSpotsCount extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "search" => $this->city->name,
            "search_id" => $this->city->id,
            "search_type" => "city",
            "count" => count($this->city->spots),
            "map_spot" => [
                "image" => $this->city->spots->first()->files->first()->file,
                "lat" =>  $this->city->spots->first()->lat,
                "lng" => $this->city->spots->first()->lng
            ]
        ];
    }
}

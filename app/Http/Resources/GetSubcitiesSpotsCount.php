<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GetSubcitiesSpotsCount extends JsonResource
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
            "search" => $this->subcity->name,
            "search_id" => $this->subcity->id,
            "search_type" => "subcity",
            "count" => count($this->subcity->spots),
            "map_spot" => [
                "image" => $this->subcity->spots->first()->files->first()->file,
                "lat" =>  $this->subcity->spots->first()->lat,
                "lng" => $this->subcity->spots->first()->lng
            ]
        ];
    }
}

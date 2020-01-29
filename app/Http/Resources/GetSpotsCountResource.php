<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GetSpotsCountResource extends JsonResource
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

                "search" => $this->name,
                "search_id" => $this->id,
                "search_type" => "country",
                "count" => count($this->spots),
                "map_spot" => [
                    "image" => $this->spots->first()->files->first()->file,
                    "lat" => $this->spots->first()->lat,
                    "lng" => $this->spots->first()->lng,
                 ]

        ];
    }
}

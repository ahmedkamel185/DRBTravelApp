<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CitySeachResource extends JsonResource
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
            "search_type" => "city",
            "count" => count($this->spots),
        ];
    }
}

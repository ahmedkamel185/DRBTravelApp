<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FollowsResource extends JsonResource
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
            "id" => $this->id,
            "title" => $this->title,
            "place_name" => "Cairo 2019",
            "location" => $this->location,
            "description" => $this->desc,
            "lat" => $this->lat,
            "lng" => $this->lng,
            "journal_id" => $this->journal_id,
            "status" => $this->status,
            "created_at" => strtotime($this->created_at) * 1000,

        ];
    }
}

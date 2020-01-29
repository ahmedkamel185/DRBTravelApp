<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PublisherSearchResource extends JsonResource
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
            "username" => $this->username,
            "verified" => $this->verified,
            "display_name" => $this->display_name,
            "image" => $this->image,
            "status" => $this->status
        ];
    }
}

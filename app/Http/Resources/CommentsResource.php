<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CommentsResource extends JsonResource
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
            "comment" => $this->comment,
            "created_at" => strtotime($this->created_at) * 1000,
            "Publisher" => [
                "id" => $this->user->id,
                "username" => $this->user->username,
                "verified" => $this->user->verified,
                "display_name" => $this->user->display_name,
                "image" => $this->user->image,
                "status" => $this->user->status
            ]
        ];
    }
}

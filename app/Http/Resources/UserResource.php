<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Block;

class UserResource extends JsonResource
{
    protected $publisher_id;

    public function publisher($value){
        $this->publisher_id = $value;
        return $this;
    }
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
            "mobile" => $this->mobile,
            "city" => $this->city,
            "email" => $this->email,
            "bio" => $this->bio,
            "status" => $this->status,
            "followers" => count($this->followers),
            "following" => count($this->follows),
            "is_blocked" => Block::where('user_id', $this->publisher_id)
                ->where('publisher_id', $this->id)->first() ? true : false,
            "spots" => count($this->spots),
            "is_followed" => $this->follows->where("follower_id", (int) $this->publisher_id)->first() ? true : false,
        ];
    }

    public static function collection($resource){
        return new UserResourceCollection($resource);
    }
}

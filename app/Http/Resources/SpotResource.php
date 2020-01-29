<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Spot;
//use App\Models\Like;

class SpotResource extends JsonResource
{
    protected $publisher_id;

    public function foo($value){
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
            "title" => $this->title,
            "place_name" => $this->title,
            "location" => $this->location,
            "description" => $this->desc,
            "lat" => $this->lat,
            "lng" => $this->lng,
            "journal_id" => $this->journal_id,
            "status" => $this->status,
            "created_at" => strtotime($this->created_at) * 1000,
            "likes_count" => count($this->likes),
            "comm_count" => count($this->comments),
            "files_count" => count($this->files),
            "favourites_count" => count($this->favourites),
            "is_favourite" => $this->favourites->where('publisher_id', (int)$this->publisher_id)->first() ? true : false,
            "is_liked" => $this->likes->where('publisher_id', (int)$this->publisher_id)->first() ? true : false,
            "files" => $this->files,
            "publisher" =>  [
                "id" => $this->publisher->id,
                "username" => $this->publisher->username,
                "verified" => $this->publisher->verified,
                "display_name" => $this->publisher->display_name,
                "image" => $this->publisher->image,
                "status" => $this->publisher->status
            ],

            "Spot_journey" => [
                "name" => isset($this->journal )? $this->journal->name : null,
                "description" => isset($this->journal ) ? $this->journal->desc : null,
                "spots_count" => isset($this->journal ) ? Spot::where("journal_id", $this->journal->id)->count() : null,
                "journey_spots" =>  isset($this->journal ) ? Spot::where("journal_id", $this->journal->id)->with("files")->get() : null,



            ]


        ];
    }

    public static function collection($resource){
        return new SpotResourceCollection($resource);
    }
}

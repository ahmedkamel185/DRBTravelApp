<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Spot;

class JournalResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */



    public function toArray($request)
    {
        $spot_found = Spot::where("journal_id", $this->id)->first();

        return [
            "id" => $this->id,
            "name" => $this->name,
            "desc" => $this->desc,
            "spots" => count($this->spots),
            "file" => $spot_found ? $spot_found->files->first()->file : null
        ];
    }
}

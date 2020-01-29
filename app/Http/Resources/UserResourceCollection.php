<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserResourceCollection extends ResourceCollection
{
    protected $publisher_id;

    public function publisher($value){
        $this->publisher_id = $value;
        return $this;
    }

    public function toArray($request)
    {
        return $this->collection->map(function (UserResource $resource) use ($request) {
            return $resource->publisher($this->publisher_id)->toArray($request);
        })->all();
    }
}

<?php

namespace App\Http\Controllers\WEB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TripResource;
use function Couchbase\defaultDecoder;

class ImagesController extends Controller
{
    //
    public function index()
    {
       $image =  TripResource::where('type','image')->orderby('created_at','DESC')->paginate(6);
        return view('images.index')
            ->with('images',$image)
            ;
    }

    public function image($id){
        $image = TripResource::find($id);
        return view('images.show')->with('image',$image);
    }
}

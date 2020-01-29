<?php

namespace App\Http\Controllers\WEB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TripResource;

class VideosController extends Controller
{
    //
    public function index()
    {
        $video =  TripResource::where('type','vedio')->orderby('created_at','DESC')->paginate(3);
        return view('videos.index')->with('videos',$video);
    }

    public function video($id){
        $image = TripResource::find($id);
        return view('videos.show')->with('video',$image);
    }
}

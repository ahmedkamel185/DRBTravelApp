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
        $video =  TripResource::where('type','vedio')->paginate(3);
        return view('videos.index')->with('videos',$video);
    }
}

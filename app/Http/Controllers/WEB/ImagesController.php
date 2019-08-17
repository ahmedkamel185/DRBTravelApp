<?php

namespace App\Http\Controllers\WEB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TripResource;

class ImagesController extends Controller
{
    //
    public function index()
    {
       $image =  TripResource::where('type','image')->paginate(3);
        return view('images.index')->with('images',$image);
    }
}

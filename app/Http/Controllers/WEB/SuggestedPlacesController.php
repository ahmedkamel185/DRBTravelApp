<?php

namespace App\Http\Controllers\WEB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SuggestedPlacesController extends Controller
{
    //
    public function index()
    {
        return view('places.index');
    }
}

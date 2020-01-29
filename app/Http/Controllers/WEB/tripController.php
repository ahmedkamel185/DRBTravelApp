<?php

namespace App\Http\Controllers\WEB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Trip;
use File;

class tripController extends Controller
{
    //
    public function index()
    {
        return view('trip.index')
            ->with('trips',Trip::orderby('created_at','DESC')->paginate(3))
            ->with('trips_count',Trip::all()->count())

            ;
    }
    public function destroy($id)
    {
        $trip = Trip::find($id);
        File::delete('uploads/trips/' . $trip->map_screen_shoot);
        foreach ($trip->resources as $resource){
            File::delete('uploads/tripResources/'.$resource->resource);
        }
        $trip->delete();
        return redirect()->back();

    }
}

<?php

namespace App\Http\Controllers\WEB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Risk;

class RoadRisksController extends Controller
{
    //
    public function index()
    {
        return view('road_risks.index')->with('risks',Risk::all());
    }

    public function changeStatus(Request $request)

    {

        $risk = Risk::find($request->user_id);

        $risk->status = $request->status;

        $risk->save();



        return response()->json(['success'=>'risk change status.']);

    }
}

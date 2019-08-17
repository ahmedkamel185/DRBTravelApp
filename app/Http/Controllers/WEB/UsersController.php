<?php

namespace App\Http\Controllers\WEB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Publisher;
use App\Models\Trip;


class UsersController extends Controller
{
    //
    public function index()
    {

        return view('users.index')
            ->with('users', Publisher::all());
    }

    public function changeStatus(Request $request)

    {

        $user = Publisher::find($request->user_id);

        $user->status = $request->status;

        $user->save();



        return response()->json(['success'=>'Status change status.']);

    }


    public function changeVerified(Request $request)

    {

        $user = Publisher::find($request->user_id);

        $user->verified = $request->verified;

        $user->save();



        return response()->json(['success'=>'Status change verified.']);

    }
    
    public function show($id)
    {

        $publisher =  Publisher::find($id);
        $trips = $publisher->trips->all();

           return view('users.show')
               ->with('user',$publisher)
               ->with('trips',$trips)
               ;







    }
}

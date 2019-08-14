<?php

namespace App\Http\Controllers\WEB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Publisher;

class UsersController extends Controller
{
    //
    public function index()
    {
        return view('users.index')->with('users', Publisher::all());
    }

    public function changeStatus(Request $request)

    {

        $user = Publisher::find($request->user_id);

        $user->status = $request->status;

        $user->save();



        return response()->json(['success'=>'Status change successfully.']);

    }

    public function show($id)
    {
        return view('users.show')->with('user', Publisher::find($id));
    }
}

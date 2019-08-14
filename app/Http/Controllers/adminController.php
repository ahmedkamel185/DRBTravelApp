<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class adminController extends Controller
{
    //dash board
    public function home()
    {
        return view('admin.login');
    }



}

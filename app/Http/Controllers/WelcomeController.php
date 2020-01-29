<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        return view("welcome");
    }

    public function  arIndex()
    {
        \App::setLocale('ar');

        return view("welcome");
    }


}

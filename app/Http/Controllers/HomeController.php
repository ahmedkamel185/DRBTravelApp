<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publisher;
use App\Models\Store;
use App\Models\Trip;
use App\Models\Risk;
class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home')
            ->with('publisher_count',Publisher::all()->count())
            ->with('count_service',Store::all()->count())
            ->with('trip_count',Trip::all()->count())
            ->with('risk_count',Risk::all()->count())

            ;
    }
}

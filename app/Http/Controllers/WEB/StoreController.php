<?php

namespace App\Http\Controllers\WEB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Store;

class StoreController extends Controller
{
    //index
    public function index()
    {
        return view('store.index')->with('stores',Store::all());
    }
}

<?php

namespace App\Http\Controllers\WEB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Suggest;
use File;

class SuggestedPlacesController extends Controller
{
    //
    public function index()
    {
        return view('places.index')->with('places',Suggest::orderby('created_at','DESC')->paginate(10));
    }
    public function add(Request $request)
    {
        return view('places.add');
    }
    public function store(Request $request)
    {
        $this->validate($request,[
           'lat'       => 'required',
           'lng'       => 'required',
           'address'   => 'required|min:3|max:191',
            'desc'     => 'required',
            'image'    =>'nullable|image'
        ]);
        $suggest = new Suggest;
        $suggest->desc = $request['desc'];
        $suggest->lat = $request['lat'];
        $suggest->lng = $request['lng'];
        $suggest->address = $request['address'];
        $suggest->user_id = auth()->user()->id;

        if ($request['image']) {
            $photo = $request->image;
            $name = date('d-m-y') . time() . rand() . '.' . $photo->getClientOriginalExtension();
            $photo->move(public_path('uploads/suggests'), $name);
            $suggest->image = $name;
        }
        $suggest->save();
        session()->flash('success', 'Place added success');
        return view('places.index')->with('places',Suggest::paginate(10));
    }



    public function edit(Request $request, $id)
    {
        $suggest = Suggest::find($id);
        return view('places.edit')->with('suggest',$suggest);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request,[
            'lat'       => 'required',
            'lng'       => 'required',
            'address'   => 'required|min:3|max:191',
            'desc'     => 'required',
            'image'    =>'nullable|image'
        ]);

        $suggest = Suggest::find($id);
        $suggest->desc = $request['desc'];
        $suggest->lat = $request['lat'];
        $suggest->lng = $request['lng'];
        $suggest->address = $request['address'];
        $suggest->user_id = auth()->user()->id;

        if ($request['image']) {
            $photo = $request->image;
            if ($suggest->image != 'default_image.png') {
                File::delete('uploads/suggests/' . $suggest->image);
            }

            $name = date('d-m-y') . time() . rand() . '.' . $photo->getClientOriginalExtension();
            $photo->move(public_path('uploads/suggests'), $name);
            $suggest->image = $name;

        }
        $suggest->save();
        session()->flash('success', 'Place Updated success');
        return view('places.index')->with('places',Suggest::paginate(10));
    }



    public function delete(Request $request, $id)
    {
        $suggest = Suggest::find($id);
        if ($suggest->image != 'default_image.png') {
            File::delete('uploads/suggests/' . $suggest->image);
        }
        $suggest->delete();
        session()->flash('success', 'Deleted success');
        return redirect()->back();
    }
}

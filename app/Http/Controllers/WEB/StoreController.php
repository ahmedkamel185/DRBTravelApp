<?php

namespace App\Http\Controllers\WEB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StorePlace;
use App\Models\StoreType;
use File;
use Validator;


class StoreController extends Controller
{
    //index
    public function index()
    {
        return view('store.index')->with('stores', Store::all());
    }


    public function changeStatus(Request $request)

    {

        $user = Store::find($request->user_id);

        $user->status = $request->status;

        $user->save();


        return response()->json(['success' => 'Status change status.']);

    }


    public function changeVerified(Request $request)

    {

        $user = Store::find($request->user_id);

        $user->verified = $request->verified;

        $user->save();


        return response()->json(['success' => 'Status change verified.']);

    }

    public function show($id)
    {
        $store = Store::find($id);
        return view('store.show')->with('store', $store);
    }

    public function deleteAddress($id)
    {
        $place = StorePlace::find($id);
        if ($place->image != 'default_image.png') {
            File::delete('uploads/storePlaces/' . $place->image);
        }
        $place->delete();
        return redirect()->back();
    }

    public function editAddress($id)
    {
        $place = StorePlace::find($id);
        return view('store.edit')->with('place', $place);
    }

    public function updateAddress(Request $request, $id)
    {
        $this->validate($request, [
            'address' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'desc' => 'required',

        ], [], [
            'address' => 'you should choose address',
            'lat' => 'you should choose position',
            'lng' => 'you should choose position'
        ]);
        $place = StorePlace::find($id);
        $place->lat = $request['lat'];
        $place->lng = $request['lng'];
        $place->address = $request['address'];
        $place->desc = $request['desc'];
        $place->save();
        return redirect()->route('store.show', ['id' => $place->store->id])->with('store', $place->store);

    }

    public function addStore()
    {
        return view('store.add')->with('store_type', StoreType::all());
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            "store_name" => 'required|min:2|max:190|unique:stores,store_name',
            'store_type' => 'required|exists:store_types,id',
            'address' => 'required|min:2|max:190',
            'mobile' => 'required|min:2|max:190|unique:stores,mobile',
            'email' => 'required|email|min:2|max:190|unique:stores,email',
            'password' => 'required|min:2|max:190',
            'city' => 'required|min:2|max:190',
            'lat' => 'required',
            'lng' => 'required'
        ]);
        $store = new Store;
        $store->store_name = $request['store_name'];
        $store->mobile = $request['mobile'];
        $store->email = $request['email'];
        $store->password = bcrypt($request['password']);
        $store->city = $request['city'];
        $store->store_type_id = $request['store_type'];
        $store->address = $request['address'];
        $store->save();
        session()->flash('success', 'Provider added success');

        return redirect()->back();
    }

    public function storeAddress($id)
    {
        $store = Store::find($id);
        return view('store.addAddress')->with('store', $store);
    }

    public function saveAddress(Request $request, $id)
    {
        $store_id = Store::find($id);
        $this->validate($request,
            [
                "lat" => 'required',
                'lng' => 'required',
                'address' => 'required|min:2|max:190',
                'image' => 'nullable|image',
                'desc' => 'required',
            ]);

        $storePlace = new StorePlace;
        $storePlace->lat = $request['lat'];
        $storePlace->lng = $request['lng'];
        $storePlace->address = $request['address'];
        $storePlace->desc = $request['desc'];
        $storePlace->store_id = $store_id->id;

        $storePlace->save();
        return redirect()->route('store.show',['id' =>$store_id->id])->with('store',$store_id);


    }
}

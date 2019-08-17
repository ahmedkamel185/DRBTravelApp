<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StorePlace;
use Validator;
use Image;
use File;




class storePlacesController extends Controller
{
    //
    //response storeType
    protected  function  responseStoreType($storeType)
    {
        $res['name_ar'] = $storeType['name_ar'];
        $res['name_en'] = $storeType['name_en'];
        $res['icon']    = asset('uploads/storeTypes').'/'.$storeType->icon;
        return $res;

    }

    // rpsonse place
    protected  function  responseStorePlace($storePlace)
    {
        $res['lat']        =   $storePlace['lat'];
        $res['lng']        =   $storePlace['lng'];
        $res['address']    =   $storePlace['address'];
        $res['desc']       =   $storePlace['desc'];
        $res['image']      =   asset('uploads/storePlace').'/'.$storePlace->image;
        return $res;

    }
    // reponse store
    protected function responseUser($user, $type=2)
    {
        $res["id"]              = $user->id;
        $res["store_name"]      = $user->store_name;
        $res["mobile"]          = $user->mobile;
        $res["email"]           = $user->email;
        $res["city"]            = $user->city;
        $image                  = is_null($user['image'])? "default_image.png" : $user['image'];
        $res['image']           = asset('uploads/publishers') . '/' . $image;
        $res['status']          = is_null($user['status'])?1:$user['status'];
        $res['verified']        = is_null($user['verified'])?1:$user['verified'];
        $res['type']            = $type;
        $res['storeType']       = $this->responseStoreType($user->StoreType);
        return $res;
    }
    // repsonse near
    protected function responsNear($place,$lat,$lng ,$unit){
        $res['places'] =  $this->responseStorePlace($place);
        $res['store']  =  $this->responseUser($place->store);
        $res['disance']=  distance($lat , $lng ,$place,$unit);
        return $res;
    }
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
        ]);
        if ($validator->passes()) {
            
            $storePlaces = StorePlace::where('store_id', $request->store_id)->where('status', true)->get();

            $data = $storePlaces->map(function ($type) {
                return $this->responseStorePlace($type);
            });
            return response()->json(
                [
                    'status' => true,
                    'data' => ['store-places' => $data],
                    'msg' => ""
                ]
            );
        } else {
            foreach ((array)$validator->errors() as $key => $value) {
                foreach ($value as $msg) {
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    public function addStorePlace(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
             "lat"                 => 'required',
             'lng'                 => 'required',
             'address'             => 'required|min:2|max:190',
             'image'               => 'nullable|image',
             'desc'                => 'required',
             'store_id'            => 'required|exists:stores,id',
            ]);

        if ($validator->passes()){
            $storePlace                       = new StorePlace;
            $storePlace->lat                  = $request['lat'];
            $storePlace->lng                  = $request['lng'];
            $storePlace->address              = $request['address'];
            $storePlace->desc                 = $request['desc'];
            $storePlace->store_id             = $request['store_id'];

            if($request['image'])
            {
                $photo=$request->image;
                $name = date('d-m-y').time().rand().'.'.$photo->getClientOriginalExtension();
                Image::make($photo)->save('uploads/storePlaces/'.$name);
                $storePlace->image = $name;

            }
            $storePlace->save();
            $msg = $request['lang'] == 'ar' ? ' تم اضافه مكان جديد.' : ' store place added successfully.';
            return response()->json(
                [
                    'status' => true,
                    'data' => ['storePlace'=>$storePlace->id],
                    'msg'=>$msg
                ]
            );
        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }

    }

    // update store
    public function updateStorePlace(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'storePlace_id'       => 'required',
                'lat'                 => 'required',
                'lng'                 => 'required',
                'address'             => 'required|min:2|max:190',
                'image'               => 'nullable|image',
                'desc'                => 'required',
                'store_id'            => 'required|exists:stores,id',
            ]);

        if ($validator->passes()) {

            $storePlace            = StorePlace::find($request->storePlace_id);
            $storePlace->lat                  = $request['lat'];
            $storePlace->lng                  = $request['lng'];
            $storePlace->address              = $request['address'];
            $storePlace->desc                 = $request['desc'];
            $storePlace->store_id             = $request['store_id'];

            if($request['image'])
            {
                $photo=$request->image;
                if($storePlace->image != 'default_image.png') {
                    File::delete('uploads/storePlaces/'.$storePlace->image);
                }

                $name = date('d-m-y').time().rand().'.'.$photo->getClientOriginalExtension();
                Image::make($photo)->save('uploads/storePlaces/'.$name);
                $storePlace->image = $name;

            }
            $storePlace->save();
            $msg = $request['lang'] == 'ar' ? 'تم تعديل المكان' : 'Store updated .';
            return response()->json(
                [
                    'status' => true,
                    'data'  => "",
                    'msg'   => $msg
                ]
            );


        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }







// delete storePlace
    public function deleteStorePlace(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'storePlace_id' => 'required|exists:store_places,id',
        ]);
        if ($validator->passes()) {
           $store_place = StorePlace::find($request->storePlace_id);

            $photo=$store_place->image;
            if($photo != 'default_image.png') {
                File::delete('uploads/storePlaces/'.$photo);
            }

            $store_place->delete();
            $msg = $request['lang'] == 'ar' ? 'تم حذف المكان' : " Place Delete success";
            return response()->json(['status' => true, 'data' => "", 'msg' => $msg]);


        } else {
            $msg = $request['lang'] == 'ar' ? 'حدث خطا ' : "Something error";
            return response()->json(['status' => false, 'msg' => $msg]);
        }

    }


    // get near store

    public  function nearPlaces(Request $request){
        $validator = Validator::make($request->all(),
            [
                'lat'                 => 'required',
                'lng'                 => 'required',
                'distance'            => 'nullable',
                'type'                => 'nullable|in:k,m'
            ]);

        if ($validator->passes()) {
            $lat        = $request['lat'];
            $lng        = $request['lng'];
            $disance    = is_null($request['distance'])?50:$request['distance'];
            $type        = 6371;
            if($request['type']=='m')
                $type  = 3959;
            $query = "SELECT id 
                    , ( $type * acos ( cos ( radians(". $lat .") ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(".  $lng .") ) + sin ( radians(". $lat .") ) * sin( radians( lat ) ) ) )
                     AS `distance` FROM `store_places`  HAVING distance <= $disance ";
            $ids          =  collect(\DB::select($query))->pluck('id')->toArray();
            $storePlaces  =  StorePlace::whereIn('id', $ids)->get();
            $unit         = $request['type'];
            $data         = $storePlaces->map(function ($place) use ($lat, $lng ,$unit){
                return $this->responsNear($place,$lat, $lng, $unit);
            });
            return response()->json(
                [
                    'status' => true,
                    'data'  => ["near"=>$data],
                    'msg'   => ""
                ]
            );
        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

}

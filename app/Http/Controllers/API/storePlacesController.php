<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\{
    StorePlace,
    Suggest,
    Risk,
    Publisher
};
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
        $res['icon']    = asset('uploads/storeTypes').'/'.$storeType['icon'];
        return $res;

    }

    // rpsonse place
    protected  function  responseStorePlace($storePlace)
    {
        $res['id']         =   $storePlace['id'];
        $res['lat']        =   $storePlace['lat'];
        $res['lng']        =   $storePlace['lng'];
        $res['address']    =   $storePlace['address'];
        $res['desc']       =   $storePlace['desc'];
        $res['image']      =   asset('uploads/storePlace').'/'.$storePlace['image'];
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
    /*************************************
     *         sugest
     * **********************************/

    //response suggest
    protected function responseSuggest($suggest, $user_id = 0)
    {
        $res["id"]              = $suggest->id;
        $res["lat"]             = $suggest->lat;
        $res["lng"]             = $suggest->lng;
        $res["address"]         = $suggest->address;
        $res["desc"]            = $suggest->desc;
        $res["user_id"]         = $suggest->user_id;
        $image                  = is_null($suggest['image'])? "default_image.png" : $suggest['image'];
        $res['image']           = asset('uploads/suggests') . '/' . $image;
        $res['comments']        = $suggest->comments->count();
        $res['likes_count']     = $suggest->likes->count();
        $res['likes_latest']    = $res['likes_count'] >0? $suggest->likes()->latest()->first()->user->display_name:"";
        $res['created_at']      = $suggest->created_at->format('d-m-Y h:i a');
        $res['publisher']       = Publisher::find($suggest->user_id);
        return $res;
    }

    // response comment
    protected function   responseComment($comment,$user_id =0){
        $res['id']             =  $comment->id;
        $res['body']           =  $comment->body;
        $res['user']           =  $this->responseUserS($comment->user);
        $res['suggest_id']     = $comment->suggest_id;
        $res['created_at']     = $comment->created_at->format('d-m-Y h:i a');
        $res['status']         = $user_id == 0? true : $user_id==$comment->user_id;
        return $res;
    }
    // response like
    protected function responseLike($like){
        $res['id']             = $like['id'];
        $res['user']           = $this->responseUserS($like->User);
        $res['suggest_id']     = $like->suggest_id;
        $res['created_at']     = $like->created_at->format('d-m-Y h:i a');
        return $res;
    }
    // response user
    protected function responseUserS($user, $type=1)
    {
        $res["id"]              = $user->id;
        $res["username"]        = $user->username;
        $res["display_name"]    = $user->display_name;
        $image                  = is_null($user['image'])? "default_image.png" : $user['image'];
        $res['image']           = asset('uploads/publishers') . '/' . $image;
        $res['type']            = $type;
        return $res;
    }



    /*=======================================*/



    /*risk*/

    protected function responseRisk($risk, $publisher_id=null)
    {
        $res['id']              = $risk->id;
        $res['lat']             =  $risk->lat;
        $res['lng']             =  $risk->lng;
        $res['address']         =  $risk->address;
        $res['desc']            = $risk->desc;

        $image                  =  is_null($risk['image'])? "default_image.png" : $risk['image'];
        $res['image']           =  asset('uploads/risks') . '/' . $image;
        $res['status']          =  $risk->status;
        $res['riskType']        = $this->responseRiskType($risk->riskType);
        $res['publisher']       = $this->responseUserS  ($risk->publisher);
        $res['yes']             = $risk->risksComment->where('vote','yes')->count();
        $res['no']              = $risk->risksComment->where('vote','no')->count();
        $res['act']             = $risk->risksComment->where('publisher_id',$publisher_id)->pluck('vote')->first();
        if (($res['act']))
        {
            $res['vote'] = true;
        }else{
            $res['vote'] = false;

        }

        return $res;
    }

    // response risk
    protected  function  responseRiskType($riskType)
    {
        $res['name_ar'] = $riskType['name_ar'];
        $res['name_en'] = $riskType['name_en'];
        $res['icon']    = asset('uploads/riskTypes').'/'.$riskType->icon;
        return $res;

    }
    /*=======================================================*/
    public function getPlace(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:store_places,id',
        ]);
        if ($validator->passes()) {

            $storePlace = StorePlace::find($request['id']);

            $data           = $this->responseStorePlace($storePlace);
            $data['store']  = $this->responseUser($storePlace->store);
            return response()->json(
                [
                    'status' => true,
                    'data' => ['place' => $data],
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


    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
        ]);
        if ($validator->passes()) {

            $storePlaces = StorePlace::where('store_id', $request->store_id)->where('status', 1)->get();

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
//                Image::make($photo)->save('uploads/storePlaces/'.$name);
                $photo->move(public_path('uploads/storePlaces'), $name);
                $storePlace->image = $name;

            }
            $storePlace->save();
            $msg = $request['lang'] == 'ar' ? ' تم اضافه مكان جديد.' : ' store place added successfully.';
            return response()->json(
                [
                    'status' => true,
                    'data' => ['storePlace'=>$storePlace],
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
//                Image::make($photo)->save('uploads/storePlaces/'.$name);
                $photo->move(public_path('uploads/storePlaces'), $name);
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
            return response()->json(['status' => true, 'data' => ['store_place'=>null], 'msg' => $msg]);


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
            $disance    = is_null($request['distance'])?5:(int)$request['distance'];
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

    /*add api*/

    //  get three
   public function  getThree(Request $request){
       $stores         = StorePlace::all();
       $suggests       = Suggest::all();
       $risks          = Risk::all();
       $data['stores'] = $stores->map(function ($store){
           return $this->responseStorePlace($store);
       })->toArray();
       $data['suggests']=  $suggests->map(function ($suggest){
           return $this->responseSuggest($suggest);
       })->toArray();
       $data['risks']   = $risks->map(function ($risks){
           return $this->responseRisk($risks);
       })->toArray();
       return response()->json(
           [
               'status' => true,
               'data'  => ["all-places"=>$data],
               'msg'   => ""
           ]
       );
   }
   //  get three near
   public function  getThreeNear(Request $request){
       $validator = Validator::make($request->all(),
           [
               'lat'                 => 'required',
               'lng'                 => 'required',
               'distance'            => 'nullable',
               'type'                => 'nullable|in:k,m'
           ]);

       if ($validator->passes()) {
           $disance        = is_null($request['distance'])?15:(int)$request['distance'];
           $placeIds       =  get_near($request['lat'], $request['lng'], $disance, $request['type']);
           $suggestIds     =  get_near($request['lat'], $request['lng'], $disance, $request['type'], "suggests");
           $riskIds        =  get_near($request['lat'], $request['lng'], $disance, $request['type'], "risks");
           $stores         = StorePlace::whereIn('id',$placeIds)->get();
           $suggests       = Suggest::whereIn('id',$suggestIds)->get();
           $risks          = Risk::whereIn('id',$riskIds)->get();
           $data['stores'] = $stores->map(function ($store){
               return $this->responseStorePlace($store);
           })->toArray();
           $data['suggests']=  $suggests->map(function ($suggest){
               return $this->responseSuggest($suggest);
           })->toArray();
           $data['risks']   = $risks->map(function ($risks){
               return $this->responseRisk($risks);
           })->toArray();
           return response()->json(
               [
                   'status' => true,
                   'data'  => ["all-places"=>$data],
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


    /*========================*/

}

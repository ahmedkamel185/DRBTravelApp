<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\TripResource;
use App\Models\Publishing;
use App\Models\Comment;
use App\Models\Favourit;
use App\Models\Like;
use App\Models\Block;
use App\Models\Follower;
use URL;
use App\Models\Publisher;
use App\Models\StorePlace;
use Image;
use Validator;
use File;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Notifications\Comment as commentNotify;
use App\Notifications\Like as LikeNotify;

class TripController extends Controller
{
     /**********************
     *     helper
     * ********************
     * */

     // response  trip
     protected  function reponseTrip($trip , $publisher = true)
     {
         $res['id']             = $trip->id;
         $res['start_lat']      = $trip->start_lat;
         $res['end_lat']        = $trip->end_lat;
         $res['start_lng']      = $trip->start_lng;
         $res['end_lng']        = $trip->end_lng;
         $res['start_address']  = $trip->start_address;
         $res['end_address']    = $trip->end_address;
         $image                 = is_null($trip['map_screen_shot'])? "default_image.png" : $trip['map_screen_shot'];
         $res['map_screen_shot']= asset('uploads/trips') . '/' . $image;
         $res['status']         = is_null($trip['status'])?0:$trip['status'];
         $res['distance']       = $trip['distance'];
         $res['estimated_duration']=$trip['estimated_duration'];
//         if($publisher)
            $res['publisher']      = $this->responseUser($trip->publisher);
//         else
//             $res['publisher_id']  = $trip->publisher_id;
         $res['created_at']     = $trip->created_at->format('d-m-Y h:i a');
         if($trip->status == 1)
         {
             $res['desc']       = is_null($trip->desc)? "":$trip->desc;
             $res['ended_at']   = empty($trip->ended_at)?" ": $trip->ended_at->format('d-m-Y h:i a');
             $interval          = $trip->created_at->diff($trip->ended_at);
             $duration          = [
                 'day'        => $interval->format('%a'),
                 'month'     => $interval->format('%m'),
                 'hours'     => $interval->format('%h'),
                 'minutes'   => $interval->format('%i'),
                 'years'     => $interval->format('%y')
             ];
             $res['duration']= $duration;
         }

         return $res;
     }

    // response user
    protected function responseUser($user, $type=1)
    {
        $res["id"]              = $user->id;
        $res["username"]        = $user->username;
        $res["display_name"]    = $user->display_name;
        $image                  = is_null($user['image'])? "default_image.png" : $user['image'];
        $res['image']           = asset('uploads/publishers') . '/' . $image;
        $res['type']            = $type;
        return $res;
    }

    protected function responseUserProfile($user, $userSeen_id)
    {

        $res["id"]              = $user->id;
        $res["username"]        = $user->username;
        $res["display_name"]    = $user->display_name;
        $res["mobile"]          = $user->mobile;
        $res["email"]           = $user->email;
        $res["city"]            = $user->city;
        $res['bio']             = is_null($user['bio'])?"":$user['bio'];
        $image                  = is_null($user['image'])? "default_image.png" : $user['image'];
        $res['image']           = asset('uploads/publishers') . '/' . $image;
        $res['status']          = is_null($user['status'])?1:$user['status'];
        $res['verified']        = is_null($user['verified'])?1:$user['verified'];
        $res['follow_status']   = $user->follows()->where('follower_id', $userSeen_id)->count() > 0;
        $res['block_status']    = $user->blockeds()->where('user_id', $userSeen_id)->count() > 0;
        $res['follower']        = $user->follows()->count();
        $res['follow']         = $user->followers()->count();
        $res['type']            = 1;
        $res['notification_count']= $user->unreadNotifications()->count();
        return $res;
    }

    // response the resource
    protected function  responseResource($resource)
    {
        $res['id']          = $resource['id'];
        $res['type']        = $resource['type'];
        $res['resource']    = asset('uploads/tripResources/'.$resource->resource);
        $res['lat']         = $resource['lat'];
        $res['lng']         = $resource['lng'];
        $res['address']     = $resource['address'];
        $res['created_at']  = $resource->created_at->format('d-m-Y h:i a');
        return $res;
    }

    // delete resoruece image
    protected function  responsePublishing($publishing, $user_id =0){
         $res['id']         = $publishing->id;
         $res['status']     = $publishing->status;
         $res['privacy']    = $publishing->privacy;
         $res['postTrip']       = $this->reponseTrip($publishing->trip);
         $res['sharer']     = "";
         $res['comments']   = $publishing->comments->count();
         $res['likes_count']= $publishing->likes->count();
         $res['likes_latest']= $res['likes_count'] >0? $publishing->likes()->latest()->first()->user->display_name:"";
         $res['share_count'] = Publishing::shareTrips($publishing->trip_id)->count();
         if($res['share_count'] > 0)
            $res['share_latest']= Publishing::shareTrips($publishing->trip_id)
                                ->latest()->first()->sharer->dispaly_name;
         else
             $res['share_latest'] = "";
         if($publishing->sharer_id)
         {
             $res['sharer']     = $this->responseUser($publishing->sharer);
         }
         if($user_id){
             $res['likes_status']  = $publishing->likes()->where('user_id', $user_id)->count() > 0;
             $res['fav_status']    = $publishing->favourits()->where('user_id', $user_id)->count() > 0;
         }
        $res['created_at'] = $publishing->created_at->format('d-m-Y h:i a');
        return $res;
    }

    // response comment
    protected function   responseComment($comment){
         $res['id']             =  $comment->id;
         $res['body']           =  $comment->body;
         $res['user']           =  $this->responseUser($comment->user);
         $res['publishing_id']  = $comment->publishing_id;
         $res['created_at']     = $comment->created_at->format('d-m-Y h:i a');
         return $res;
    }

    // response share-user

    protected function responseShareUser($share){
         $res['publishing_id']  = $share['id'];
         $res['user']           = $this->responseUser($share->user);
         $res['created_at']     = $share->created_at->format('d-m-Y h:i a');
         return $res;
    }

    // response like
    protected function responseLike($like){
         $res['id']             = $like['id'];
         $res['user']           = $this->responseUser($like->User);
         $res['publishing_id']  = $like->publishing_id;
         $res['created_at']     = $like->created_at->format('d-m-Y h:i a');
         return $res;
    }

    //=====================================================================
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
    protected function responseUserStore($user, $type=2)
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
    protected function responsNear($place){
        $res['places'] =  $this->responseStorePlace($place);
        $res['store']  =  $this->responseUserStore($place->store);
        return $res;
    }
    //============================================================

     /*================================*/
     // start trip
    public function startTrip(Request $request)
    {
        $validator=Validator::make($request->all(),[
            "start_lat"           => 'required',
            'start_lng'          => 'required',
            'start_address'       => 'required|min:2|max:190',
            'end_lat'             => 'required',
            'end_lng'             => 'required',
            'end_address'         => 'required|min:2|max:190',
            'map_screen_shot'     => 'nullable',
            'distance'            => 'required',
            'estimated_duration'  => 'required|min:2|max:190',
            'publisher_id'        => 'required|exists:publishers,id',

        ]);
        if ($validator->passes()) {
            $check            = Trip::where('publisher_id', $request['publisher_id'])
                            ->where('status',0)->first();
            if($check){
                $msg = $request['lang'] == 'ar' ? ' لايمكن بدء رحله قبل انتهاء من الرحله القديمه.' : ' you can\'t start trip befor finsh the current.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => ['trip'=>['id'=>null]],
                        'msg'=>$msg
                    ]
                );
            }
            $trip                       = new Trip;
            $trip->start_lat            = $request['start_lat'];
            $trip->start_lng            = $request['start_lng'];
            $trip->start_address        = $request['start_address'];
            $trip->end_lat              = $request['end_lat'];
            $trip->end_lng              = $request['end_lng'];
            $trip->end_address          = $request['end_address'];
            $trip->distance             = $request['distance'];
            $trip->estimated_duration   = $request['estimated_duration'];
            $trip->publisher_id         = $request['publisher_id'];
            if($request['map_screen_shot'])
            {
                $name  = upload_img($request['map_screen_shot'], public_path('uploads/trips/'));
                $trip->map_screen_shot = $name;

            }
            $trip->save();
            $date  = $this->reponseTrip($trip);
            publisher_log(
                $request['publisher_id'],
                'لقد قمت ببدء رحله',
                'you start trip'
            );
            $msg = $request['lang'] == 'ar' ? ' تم بدء الرحله بنجاح.' : ' sucessfull start trip.';
            return response()->json(
                [
                    'status' => true,
                    'data' => ['trip'=>$date],
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

    // home trip
    public  function currentTrip(Request $request){
        $validator=Validator::make($request->all(),[
            'publisher_id'        => 'required|exists:publishers,id',
        ]);
        if ($validator->passes()) {
            $trip        = Trip::where('publisher_id', $request['publisher_id'])
                                ->where('status', 0)->first();
            $data = [];
            if($trip){
                $data      = $this->reponseTrip($trip, false);
                $near_start= get_near_stores($trip->start_lat, $trip->start_lng,10);
                $near_end  = get_near_stores($trip->end_lat, $trip->end_lng,10);
                $all        = array_merge($near_end, $near_start);
                $storePlaces  =  StorePlace::whereIn('id', $all)->get();
                $dataPlace    = $storePlaces->map(function ($place){
                    return $this->responsNear($place);
                });

                $res['trip']         = $data;
                $res['nearStore']    = $dataPlace;

                return response()->json(
                    [
                        'status' => true,
                        'data'  => ['currentTrip'=>$res],
                        'msg'   =>""
                    ]
                );
            }
            else
            {
                return response()->json(
                    [
                        'status' => false,
                        'data'  => ['id'=>null],
                        'msg'   =>""
                    ]
                );
            }

        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // get trip

    public  function getTrip(Request $request){
        $validator=Validator::make($request->all(),[
            'trip_id'             => 'required|exists:trips,id'
        ]);
        if ($validator->passes()) {
            $trip        = Trip::where('id', $request['trip_id'])
                ->where('status', 1)->first();
            $data = [];
            if($trip){
                $data  = $this->reponseTrip($trip);
                return response()->json(
                    [
                        'status' => true,
                        'data'  => ['ended_trip'=>$data],
                        'msg'   =>""
                    ]
                );
            }
            else
            {
                return response()->json(
                    [
                        'status' => false,
                        'data'  => ['trip'=>$data],
                        'msg'   =>""
                    ]
                );
            }

        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // public function getTrips
    // finsh trip
    public function endTrip(Request $request){
        $validator=Validator::make($request->all(),[
            'trip_id'        => 'required|exists:trips,id',
            'publisher_id'   => 'required|exists:publishers,id',
            'desc'           => 'nullable',
            'privacy'        => 'required|in:public,private,flowers',
            'status'         =>  'required|in:0,1' // 0 for not publsihin now  1 publishing now

        ]);
        if ($validator->passes()) {

            $trip                = Trip::where('id',$request['trip_id'])
                ->where('publisher_id', $request['publisher_id'])->first();
            if($trip){
               if($trip->status){
                   $msg = $request['lang'] == 'ar' ? ' هذه الرحله تم الانهاء منها.' : ' this trip already finshed.';
                   return response()->json(
                       [
                           'status' => false,
                           'data' => ['trip'=> ['id'=>null] ],
                           'msg'=>$msg
                       ]
                   );
                }
               $trip->status    = 1;
               $trip->desc      = $request['desc'];
               $trip->ended_at  = Carbon::now();
               $trip->update();
               $publishing               = new Publishing;
               $publishing->trip_id      = $trip->id;
               $publishing->status       = $request['status'];
               $publishing->publisher_id = $request['publisher_id'];
               $publishing->privacy      = $request['privacy'];
               $publishing->save();
                publisher_log(
                    $request['publisher_id'],
                    'لقد قمت بانهاء رحله',
                    'you end trip'
                );
               $msg = $request['lang'] == 'ar' ? ' تم الانهاء بنجاح.' : ' sucessfull  finish .';
                return response()->json(
                    [
                        'status' => true,
                        'data' => ['trip'=>['id'=>null]],
                        'msg'=>$msg
                    ]
                );

            }else{
                $msg = $request['lang'] == 'ar' ? ' المستخدم لايملك هذه الرحله.' : ' user not owner the trip.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => ['trip'=>['id'=>null]],
                        'msg'=>$msg
                    ]
                );
            }
        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }


    // upload resource for trips
    public  function uploadResource(Request $request){

        $validator=Validator::make($request->all(),[
            'trip_id'        => 'required|exists:trips,id',
            'type'           => 'required|in:vedio,image',
            'desc'           => 'nullable',
            'resource'       => 'required',
            'lat'            => 'required',
            'lng'            => 'required',
            'address'        => 'required'

        ]);
        if ($validator->passes()) {
            $trip                = Trip::find($request['trip_id']);
            $resource            = new TripResource;
            $resource->type      = $request['type'];
            $resource->desc      = $request['desc'];
            $resource->trip_id   = $trip->id;
            $resource->lat       = $request['lat'];
            $resource->lng       = $request['lng'];
            $resource->address   = $request['address'];
            if($request['type'] == "image"){
                $photo=$request->resource;
                $name = date('d-m-y').time().rand().'.'.$photo->getClientOriginalExtension();
                Image::make($photo)->save('uploads/tripResources/'.$name);
            }
            else
            {
                $vedio    = $request->resource;
                $path     = \Storage::putFile('photos', $vedio);
                return response()->json(['path'=>$path]);
//                $name     = date('d-m-y').time().rand().'.'.$vedio->getClientOriginalExtension();
//                $vedio->move(public_path('uploads/tripResources'), $name);
            }
            $resource->resource = $name;
            $resource->save();
            publisher_log(
                $resource->trip->publisher_id,
                ' لقد قمت برفع وسائط جديده  ',
                'you upload new media'
            );
            $msg = $request['lang'] == 'ar' ? ' تم الاضافه بنجاح.' : ' sucessfull upload .';
            return response()->json(
                [
                    'status' => true,
                    'data' => ['resource'=>["id"=>null]],
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

    // delete resource
    public  function deleteResource(Request $request){
        $validator=Validator::make($request->all(),[
            'resource_id'        => 'required|exists:trip_resources,id',
        ]);
        if ($validator->passes()) {
            $resource     = TripResource::find($request['resource_id']);
            File::delete('uploads/tripResources/'.$resource->resource);
            $resource->delete();
            $msg = $request['lang'] == 'ar' ? ' تم الحذف.' : ' sucessfull delete.';
            return response()->json(
                [
                    'status' => true,
                    'data' => "",
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

    // get resoorces trips
    public function  getTripResources(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'trip_id'        => 'required|exists:trips,id',
            'user_id'        => 'required|exists:publishers,id'

        ]);

            $trip                = Trip::find($request['trip_id']);
            if($trip){
                $data  =  $trip->resources->map(function ($resource){
                   return $this->responseResource($resource);
                });
                return response()->json(
                    [
                        'status' => true,
                        'data'   => ['resources' => $data ],
                        'msg'    =>""
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

    //update resourece
    // update basic info trip
    public function  updateResource(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'resource_id'        => 'required|exists:trip_resources,id',
            'user_id'            => 'required|exists:publishers,id',
            'type'               => 'required|in:vedio,image',
            'desc'               => 'nullable',
            'resource'           => 'nullable|max:30000000',
            'lat'                => 'required',
            'lng'                => 'required',
            'address'            => 'required'

        ]);
        if ($validator->passes()) {
            $resource            = TripResource::find($request['resource_id']);

            if($resource->trip->publisher_id == $request['user_id']){
                $resource->type      = $request['type'];
                $resource->desc      = $request['desc'];
                $resource->lat       = $request['lat'];
                $resource->lng       = $request['lng'];
                $resource->address   = $request['address'];
                if($request['resource']){
                    File::delete('uploads/tripResources/'.$resource->resource);
                    if($request['type'] == "image"){
                        $photo=$request->resource;
                        $name = date('d-m-y').time().rand().'.'.$photo->getClientOriginalExtension();
                        Image::make($photo)->save('uploads/tripResources/'.$name);
                    }
                    else
                    {
                        $vedio    = $request->resource;
                        $name     = date('d-m-y').time().rand().'.'.$vedio->getClientOriginalExtension();
                        $vedio->move(public_path('uploads/tripResources'), $name);
                    }
                    $resource->resource = $name;
                }
                $resource->update();
                $msg = $request['lang'] == 'ar' ? ' تم التعديل.' : ' update sucessful.';
                return response()->json(
                    [
                        'status' => true,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }else{
                $msg = $request['lang'] == 'ar' ? ' المستخدم لايملك هذه الرحله.' : ' user not owner the trip.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }

        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // delete trip
    public function  deleteTrip(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'trip_id'        => 'required|exists:trips,id',
            'user_id'        => 'required|exists:publishers,id'

        ]);
        if ($validator->passes()) {
            $trip                = Trip::where('id',$request['trip_id'])
                ->where('publisher_id', $request['user_id'])->first();
            if($trip){
                deleteTrip($trip);
                publisher_log(
                    $request['user_id'],
                    ' لقد قمت بحذف رحله ',
                    'you delete trip'
                );
                $msg = $request['lang'] == 'ar' ? ' تم الحذف.' : ' sucessfull delete.';
                return response()->json(
                    [
                        'status' => true,
                        'data' => ['resource'=>""],
                        'msg'=>$msg
                    ]
                );
            }else{
                $msg = $request['lang'] == 'ar' ? ' المستخدم لايملك هذه الرحله.' : ' user not owner the trip.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => ['resource'=>""],
                        'msg'=>$msg
                    ]
                );
            }

        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // update basic info trip
    public function  updateTrip(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'trip_id'             => 'required|exists:trips,id',
            'user_id'             => 'required|exists:publishers,id',
            "start_lat"           => 'required',
            'start_lng'           => 'required',
            'start_address'       => 'required|min:2|max:190',
            'end_lat'             => 'required',
            'end_lng'             => 'required',
            'end_address'         => 'required|min:2|max:190',
            'map_screen_shot'     => 'nullable|image',
            'distance'            => 'required',
            'estimated_duration'  => 'required|min:2|max:190',
        ]);
        if ($validator->passes()) {
            $trip                = Trip::where('id',$request['trip_id'])
                ->where('publisher_id', $request['user_id'])->first();
            if($trip){
                $trip->start_lat            = $request['start_lat'];
                $trip->start_lng            = $request['start_lng'];
                $trip->start_address        = $request['start_address'];
                $trip->end_lat              = $request['end_lat'];
                $trip->end_lng              = $request['end_lng'];
                $trip->end_address          = $request['end_address'];
                $trip->distance             = $request['distance'];
                $trip->estimated_duration   = $request['estimated_duration'];
                if($request['map_screen_shot'])
                {
                    File::delete('uploads/trips/'.$trip->map_screen_shot);
                    $photo=$request->map_screen_shot;
                    $name = date('d-m-y').time().rand().'.'.$photo->getClientOriginalExtension();
                    Image::make($photo)->save('uploads/stores/'.$name);
                    $trip->map_screen_shot = $name;

                }
                $trip->update();
                $msg = $request['lang'] == 'ar' ? ' تم التعديل.' : ' update sucessful.';
                return response()->json(
                    [
                        'status' => true,
                        'data' => ["ended_trip"=> ['id'=>null] ],
                        'msg'=>$msg
                    ]
                );
            }else{
                $msg = $request['lang'] == 'ar' ? ' المستخدم لايملك هذه الرحله.' : ' user not owner the trip.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }

        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // change desc
    // update basic info trip
    public function  changeDescTrip(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'trip_id'             => 'required|exists:trips,id',
            'user_id'             => 'required|exists:publishers,id',
            'desc'                =>  'nullable'
        ]);
        if ($validator->passes()) {
            $trip                = Trip::where('id',$request['trip_id'])
                ->where('publisher_id', $request['user_id'])->first();
            if($trip){
                $trip->desc  = $request['desc'];
                $trip->update();
                $msg = $request['lang'] == 'ar' ? ' تم التعديل.' : ' update sucessful.';
                return response()->json(
                    [
                        'status' => true,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }else{
                $msg = $request['lang'] == 'ar' ? ' المستخدم لايملك هذه الرحله.' : ' user not owner the trip.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }

        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    /**********************
     *      publishing
     *          post
     * *******************/

    // share the trip
    public function shareTrip(Request $request){
        $validator=Validator::make($request->all(),[
            'trip_id'        => 'required|exists:trips,id',
            'publisher_id'   => 'required|exists:publishers,id',
            'sharer_id'       => 'required|exists:publishers,id',
            'privacy'        => 'required|in:public,private,flowers',
        ]);
        if ($validator->passes()) {

            $trip                = Trip::where('id',$request['trip_id'])
                ->where('publisher_id', $request['publisher_id'])->first();
            if($trip){
                // check if share is publisher
                if( $request['publisher_id'] == $request['sharer_id'] ){
                    $msg = $request['lang'] == 'ar' ? 'لا يمكن مشاركة منشوراتك.' : 'you can\' share the trip.';
                    return response()->json(
                        [
                            'status' => false,
                            'data' => "",
                            'msg'=>$msg
                        ]
                    );
                }
                $check                    = Publishing::where('trip_id', $trip->id)
                                            ->where('sharer_id', $request['sharer_id'])->first();
                // check if the sharer share it before
                if($check){
                    $msg = $request['lang'] == 'ar' ? 'لقد قمت بمشاركة الرحله من قبل.' : 'al-ready shared the trip.';
                    return response()->json(
                        [
                            'status' => false,
                            'data' => "",
                            'msg'=>$msg
                        ]
                    );
                }
                $publishing               = new Publishing;
                $publishing->trip_id      = $trip->id;
                $publishing->status       = 1;
                $publishing->type         = "share";
                $publishing->publisher_id = $request['publisher_id'];
                $publishing->privacy      = $request['privacy'];
                $publishing->sharer_id    = $request['sharer_id'];
                $publishing->save();
                $msg = $request['lang'] == 'ar' ? ' تم المشاركه بنجاح.' : 'sucessfull sharing.';
                publisher_log(
                    $request['sharer_id'],
                    ' لقد قمت بمشاركة رحله ل '.$publishing->publisher->display_name,
                    'you share the trip to'.$publishing->publisher->display_name
                );
                return response()->json(
                    [
                        'status' => true,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }else{
                $msg = $request['lang'] == 'ar' ? ' المستخدم لايملك هذه الرحله.' : ' user not owner the trip.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }
        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // get the post
    public function  getPublishing(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'publishing_id'  => 'required|exists:publishings,id',
            'user_id'        => 'required|exists:publishers,id'

        ]);
        if ($validator->passes()) {
            $publishing                = Publishing::find($request['publishing_id']);
            return response()->json(
                [
                    'status' => true,
                    'data' => ['publishing'=> $this->responsePublishing($publishing, $request['user_id'])],
                    'msg'=>""
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

    // get the share
    public function  deleteShare(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'publishing_id'  => 'required|exists:publishings,id',
            'user_id'        => 'required|exists:publishers,id'
        ]);
        if ($validator->passes()) {
            $publishing                = Publishing::where('id',$request['publishing_id'])
                ->where('sharer_id',$request['user_id'])->first();
            if($publishing){
                $publishing->delete();
                $msg = $request['lang'] == 'ar' ? ' تم الحذف.' : ' sucessfull delete.';
                return response()->json(
                    [
                        'status' => true,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }else{
                $msg = $request['lang'] == 'ar' ? ' المستخدم لايملك هذه الرحله.' : ' user not owner the trip.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }

        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // chnage privacy

    // get the share
    public function  changePrivacy(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'publishing_id'  => 'required|exists:publishings,id',
            'user_id'        => 'required|exists:publishers,id',
            'privacy'        => 'required|in:public,private,flowers',
        ]);
        if ($validator->passes()) {
            $publishing                = Publishing::where('id',$request['publishing_id'])
                ->where('publisher_id', $request['user_id'])
                ->orWhere('sharer_id',$request['user_id'])->first();
            if($publishing){
                $publishing->privacy   =  $request->privacy;
                $publishing->update();
                $msg = $request['lang'] == 'ar' ? ' تم تعديل.' : ' sucessfull update.';
                return response()->json(
                    [
                        'status' => true,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }else{
                $msg = $request['lang'] == 'ar' ? ' المستخدم لايملك هذه الرحله.' : ' user not owner the trip.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }

        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // save comment
    public function saveComment(Request $request){
        $validator=Validator::make($request->all(),[
            'publishing_id'  => 'required|exists:publishings,id',
            'user_id'        => 'required|exists:publishers,id',
            'body'           => 'required',
        ]);

        if ($validator->passes()) {
            $publishing                = Publishing::find($request['publishing_id']);
            if($publishing->privacy != "private"){
                $comment                = new Comment;
                $comment->publishing_id = $request['publishing_id'] ;
                $comment->user_id       = $request['user_id'] ;
                $comment->body          = $request['body'] ;
                $comment->save();
                $publishing->publisher->notify(new commentNotify
                     (
                        $publishing->publisher,
                        "publishing",
                        $publishing->id,
                        $comment
                    )
                );
                publisher_log(
                    $request['user_id'],
                    ' لقد قمت باضافة تعليق ل '.$comment->publishing->publisher->display_name,
                    'you add comment to'.$comment->publishing->publisher->display_name
                );

                $msg = $request['lang'] == 'ar' ? ' تم اضافة التعليق.' : ' sucessfull add comment.';
                return response()->json(
                    [
                        'status' => true,
                        'data'   => [ "comment" => $this->responseComment($comment) ],
                        'msg'    =>$msg
                    ]
                );
            }else{
                $msg = $request['lang'] == 'ar' ? ' ليس لديك صلاحيه للتعليق .' : ' privacy not allow you to comment.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }

        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // delete comment
    public function deleteComment(Request $request){

        $validator=Validator::make($request->all(),[
            'comment_id'     => 'required|exists:comments,id',
            'user_id'        => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {
            $comment                = Comment::find($request['comment_id']);
            if( $comment->user_id == $request['user_id'] ){
                $comment->delete();
                $msg = $request['lang'] == 'ar' ? ' تم حذف التعليق.' : ' sucessfull delete comment.';
                return response()->json(
                    [
                        'status' => true,
                        'data'   => "",
                        'msg'    =>$msg
                    ]
                );
            }else{
                $msg = $request['lang'] == 'ar' ? ' التعليق غير موجود ف تعليقاتك .' : 'comment not found in comments.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }

        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // get comment
    public function getComment(Request $request){

        $validator=Validator::make($request->all(),[
            'comment_id'     => 'required|exists:comments,id',
            'user_id'        => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {
            $comment                = Comment::find($request['comment_id']);
            if( $comment->user_id == $request['user_id'] ){
                return response()->json(
                    [
                        'status' => true,
                        'data'   =>[ "comment" => $this->responseComment($comment) ],
                        'msg'    =>""
                    ]
                );
            }else{
                $msg = $request['lang'] == 'ar' ? ' التعليق غير موجود ف تعليقاتك .' : 'comment not found in comments.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }

        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // update comment
    public function updateComment(Request $request){

        $validator=Validator::make($request->all(),[
            'comment_id'     => 'required|exists:comments,id',
            'user_id'        => 'required|exists:publishers,id',
            'body'           => 'required'
        ]);

        if ($validator->passes()) {
            $comment                = Comment::find($request['comment_id']);
            if( $comment->user_id == $request['user_id'] ){
                $comment->body    = $request['body'];
                return response()->json(
                    [
                        'status' => true,
                        'data'   => [ "comment" => $this->responseComment($comment) ],
                        'msg'    =>""
                    ]
                );
            }else{
                $msg = $request['lang'] == 'ar' ? ' التعليق غير موجود ف تعليقاتك .' : 'comment not found in comments.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }

        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // get all comment
    public function getComments(Request $request){

        $validator=Validator::make($request->all(),[
            'publishing_id'  => 'required|exists:publishings,id',
            'user_id'        => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {
            $publishing                = Publishing::find($request['publishing_id']);

            // check privacy
            if($publishing->privacy != "private"){
                $data  = $publishing->comments->map(function ($comment){
                    return $this->responseComment($comment);
                }) ;
                return response()->json(
                    [
                        'status' => true,
                        'data'   =>['comments'=> $data],
                        'msg'    =>""
                    ]
                );
            }else{
                $msg = $request['lang'] == 'ar' ? ' ليس لديك صلاحيه للتعليق .' : ' privacy not allow you to comment.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }


        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }


    // likes action delete and add
    public function likeAction(Request $request){
        $validator=Validator::make($request->all(),[
            'publishing_id'  => 'required|exists:publishings,id',
            'user_id'        => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {
            $publishing                = Publishing::find($request['publishing_id']);
            if($publishing->privacy != "private"){
                $like     = Like::where('publishing_id', $request['publishing_id'])
                                  ->where('user_id', $request['user_id'])->first();
                if($like) {
                    $msg = $request['lang'] == 'ar' ? ' تم حذف الاعجاب.' : ' sucessfull delete liked.';
                    publisher_log(
                        $request['user_id'],
                        ' لقد قمت بحذف الاعجاب ل '.$like->publishing->publisher->display_name,
                        'you delete the like for'.$like->publishing->publisher->display_name
                    );
                    $like->delete();
                } else{
                    $like                   = new Like;
                    $like->user_id          = $request['user_id'];
                    $like->publishing_id    = $request['publishing_id'];
                    $like->save();
                    $msg = $request['lang'] == 'ar' ? ' تم الاعجاب.' : ' sucessfull liked.';
                    $publishing->publisher->notify(new LikeNotify
                        (
                            $publishing->publisher,
                            "publishing",
                            $publishing->id,
                            $like
                        )
                    );
                    publisher_log(
                        $request['user_id'],
                        ' لقد قمت  بالاعجاب ل '.$like->publishing->publisher->display_name,
                        'you do the like for'.$like->publishing->publisher->display_name
                    );
                }

                return response()->json(
                    [
                        'status' => true,
                        'data'   => "",
                        'msg'    =>$msg
                    ]
                );
            }else{
                $msg = $request['lang'] == 'ar' ? ' ليس لديك صلاحيه للتعليق .' : ' privacy not allow you to comment.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }

        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // list like
    public function getLikes(Request $request){

        $validator=Validator::make($request->all(),[
            'publishing_id'  => 'required|exists:publishings,id',
            'user_id'        => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {
            $publishing                = Publishing::find($request['publishing_id']);

            // check privacy
            if($publishing->privacy != "private"){
                $data  = $publishing->likes->map(function ($like){
                    return $this->responseLike($like);
                }) ;
                return response()->json(
                    [
                        'status' => true,
                        'data'   =>['likes'=> $data],
                        'msg'    =>""
                    ]
                );
            }else{
                $msg = $request['lang'] == 'ar' ? ' ليس لديك صلاحيه للتعليق .' : ' privacy not allow you to comment.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }


        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }


    // list share
    public function getShares(Request $request){

        $validator=Validator::make($request->all(),[
            'publishing_id'  => 'required|exists:publishings,id',
            'user_id'        => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {
            $publishing                = Publishing::find($request['publishing_id']);

            // check privacy
            if($publishing->privacy != "private"){
                $data  = Publishing::shareTrips($publishing->trip_id)->get()->map(function ($share){
                    return $this->responseShareUser($share);
                });
                return response()->json(
                    [
                        'status' => true,
                        'data'   =>['shares'=> $data],
                        'msg'    =>""
                    ]
                );
            }else{
                $msg = $request['lang'] == 'ar' ? ' ليس لديك صلاحيه للتعليق .' : ' privacy not allow you to comment.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }


        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // favoutit action
    public function favAction(Request $request){
        $validator=Validator::make($request->all(),[
            'publishing_id'  => 'required|exists:publishings,id',
            'user_id'        => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {
            $publishing                = Publishing::find($request['publishing_id']);
            if($publishing->privacy != "private"){
                $fav     = Favourit::where('publishing_id', $request['publishing_id'])
                    ->where('user_id', $request['user_id'])->first();
                if($fav) {
                    $msg = $request['lang'] == 'ar' ? ' تم حذف من المفضله.' : ' sucessfull delete from favourit.';
                    $fav->delete();
                    publisher_log(
                        $request['user_id'],
                        ' لقد قمت بحذف منشور من المفضل ل '.$fav->publishing->publisher->display_name,
                        'you delete the post from favourits for'.$fav->publishing->publisher->display_name
                    );
                } else{
                    $fav                   = new Favourit;
                    $fav->user_id          = $request['user_id'];
                    $fav->publishing_id    = $request['publishing_id'];
                    $fav->save();
                    publisher_log(
                        $request['user_id'],
                        ' لقد قمت باضافة منشور الى المفضل ل '.$fav->publishing->publisher->display_name,
                        'you add the post to favourits for'.$fav->publishing->publisher->display_name
                    );
                    $msg = $request['lang'] == 'ar' ? ' تم الاضافه الى المفضله.' : ' sucessfull add to favourit.';
                }

                return response()->json(
                    [
                        'status' => true,
                        'data'   => "",
                        'msg'    =>$msg
                    ]
                );
            }else{
                $msg = $request['lang'] == 'ar' ? ' ليس لديك صلاحيه للتعليق .' : ' privacy not allow you to comment.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }

        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // get favourits
    public function getFavs(Request $request){
        $validator=Validator::make($request->all(),[
            'publishing_id'  => 'required|exists:publishings,id',
            'user_id'        => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {
            $publishing                = Publishing::find($request['publishing_id']);

            // check privacy
            if($publishing->privacy != "private"){
                $data  = $publishing->favourits->map(function ($fav){
                    $res['id']           = $fav['id'];
                    $res['publishing']   = $this->responsePublishing($fav->publishing);
                    return $res;
                }) ;
                return response()->json(
                    [
                        'status' => true,
                        'data'   =>['favourits'=> $data],
                        'msg'    =>""
                    ]
                );
            }else{
                $msg = $request['lang'] == 'ar' ? ' ليس لديك صلاحيه للتعليق .' : ' privacy not allow you to comment.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }


        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }

    }

    public function  ChangeStatusPublisher(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'trip_id'        => 'required|exists:trips,id',
            'user_id'        => 'required|exists:publishers,id',
            'status'         =>  'required|in:1,2,3',
        ]);
        if ($validator->passes()) {
            $publishing                = Publishing::publishTrip($request['trip_id'])
                                        ->where('publisher_id',$request['user_id'])->first();
            if($publishing){
                if($publishing->status == 1)
                {
                    $publishing->status = 2;
                    $publishing->update();
                    $msg = $request['lang'] == 'ar' ? ' تم النشر.' : ' sucessfull delete.';
                    return response()->json(
                        [
                            'status' => true,
                            'data' => ['publishing'=>""],
                            'msg'=>$msg
                        ]
                    );
                }
                else
                {
                    $msg = $request['lang'] == 'ar' ? '  لايمكن  نشر هذه الرحله.' : ' can\'t publish this الرحله.';
                    return response()->json(
                        [
                            'status' => true,
                            'data' => ['publishing'=>""],
                            'msg'=>$msg
                        ]
                    );
                }

            }else{
                $msg = $request['lang'] == 'ar' ? ' المستخدم لايملك هذه الرحله او لم يتم الانتهاء منها.' : ' user not owner the trip or not end yet.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }

        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }


    /*=============================================*/

    /********************
     *  pagnation trip
     * ******************/

    // public trip
    public function publicTrip(Request $request){
        $validator=Validator::make($request->all(),[
            'user_id'        => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {
            $blocks       = Block::buldBlockId($request['user_id']);
            $blockingMe   = Block::buldBlockerId($request['user_id']);
            $allBlocks    = array_merge($blocks, $blockingMe);
            $publsihng    = Publishing::where('privacy', 'public')
                                ->where('block', 0)
                                ->whereNotIn('publisher_id', $allBlocks )
                                ->WhereNotIn('sharer_id', $allBlocks)
                                ->latest()
                                ->simplePaginate(10);
            $meta       = getBasicInfoPagantion($publsihng);
            $data       = getCollectionPagantion($publsihng)->map(function ($publishing) use($request){
                return $this->responsePublishing($publishing, $request['user_id']);
            });
            $res['pulishings'] = $data;
            $res['meta']        = $meta;
            return response()->json(
                [
                    'status' => true,
                    'data'   =>['publishings'=> $res],
                    'msg'    =>"",
//                    'meta'   => $meta
                ]
            );

        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json([ 'status' => false, 'msg' => $msg[0] ]);
                }
            }
        }

    }

    // private trip

    // public trip
    public function followerTrip(Request $request){
        $validator=Validator::make($request->all(),[
            'user_id'        => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {
            $blocks       = Block::buldBlockId($request['user_id']);
            $blockingMe   = Block::buldBlockerId($request['user_id']);
            $followers    = Follower::buldFollowerId($request['user_id']);
            $allBlocks    = array_merge($blocks, $blockingMe);
            $publsihng    = Publishing::where('privacy', 'flowers')
                            ->where('block', 0)
                            ->whereIn('publisher_id', $followers)
                            ->whereNotIn('publisher_id', $allBlocks )
                            ->WhereNotIn('sharer_id', $allBlocks)
                            ->latest()
                            ->simplePaginate(10);
            $meta       = getBasicInfoPagantion($publsihng);
            $data       = getCollectionPagantion($publsihng)->map(function ($publishing) use($request){
                return $this->responsePublishing($publishing, $request['user_id']);
            });
            return response()->json(
                [
                    'status' => true,
                    'data'   =>['publishings'=> $data],
                    'msg'    =>"",
                    'meta'   => $meta
                ]
            );

        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json([ 'status' => false, 'msg' => $msg[0] ]);
                }
            }
        }

    }

    // get the user profile
    public function publisherProfile(Request $request){
        $validator=Validator::make($request->all(),[
            'user_id'        => 'required|exists:publishers,id',
            'publisher_id'   => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {

            $followers    = Follower::buldFollowerId($request['publisher_id']);
            if($request['publisher_id'] == $request['user_id']){
                $publsihng    = Publishing::whereIn('privacy', ['public','private','flowers'] )
                    ->where('publisher_id', $request['publisher_id'] )
                    ->Where('sharer_id', $request['publisher_id'])
                    ->latest()
                    ->simplePaginate(10);
            } else if(in_array($request['user_id'], $followers))
            {

                $publsihng    = Publishing::whereIn('privacy', ['public','flowers'] )
                    ->where('publisher_id', $request['publisher_id'] )
                    ->Where('sharer_id', $request['publisher_id'])
                    ->simplePaginate(10);
            } else{
                $publsihng    = Publishing::where('privacy', 'public' )
                    ->where('publisher_id', $request['publisher_id'] )
                    ->Where('sharer_id', $request['publisher_id'])
                    ->simplePaginate(10);
            }
            $meta       = getBasicInfoPagantion($publsihng);
            $publisher  = Publisher::find($request['publisher_id']);
            $arr['publisher']=$this->responseUserProfile($publisher, $request['user_id']);
            $data       = getCollectionPagantion($publsihng)->map(function ($publishing) use($request){
                return $this->responsePublishing($publishing, $request['user_id']);
            });
//            $arr['publishings'] = $data;
            return response()->json(
                [
                    'status' => true,
                    'data'   =>['profile'=> $data],
                    'msg'    =>"",
                    'meta'   => $meta
                ]
            );

        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json([ 'status' => false, 'msg' => $msg[0] ]);
                }
            }
        }

    }
    // get the post
    public function  getPublishingUser(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'user_id'        => 'required|exists:publishers,id'

        ]);
        if ($validator->passes()) {
            $publishing                = Publishing::whereNull("sharer_id")
                ->where('publisher_id', $request['user_id'])->simplePaginate(10);

            $meta       = getBasicInfoPagantion($publishing);

            $data       = getCollectionPagantion($publishing)->map(function ($publishing) use($request){
                return $this->responsePublishing($publishing, $request['user_id']);
            });
            return response()->json(
                [
                    'status' => true,
                    'data'   =>['publishings'=> $data],
                    'msg'    =>"",
                    'meta'   => $meta
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
    // test
    public function test(Request $request){
        // Create a client with a base URI
        phpInfo();
        $origin       = "30.044281,31.340002";
        $destination  = "31.037933,31.381523";
        $client = new Client([
//            'base_uri' => 'https://foo.com/api/',
        ]);
        $response = $client->get("https://maps.googleapis.com/maps/api/directions/json?origin=" . $origin . "&destination=" . $destination . "&waypoints=&mode=driving&language=ar&key=AIzaSyDYjCVA8YFhqN2pGiW4I8BCwhlxThs1Lc0");;
        $places   = json_decode($response->getBody()->getContents())->routes[0]->legs[0]->steps;
        dd(count($places));
    }


}

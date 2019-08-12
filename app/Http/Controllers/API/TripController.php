<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\TripResource;
use App\Models\Publishing;
use URL;
use Image;
use Validator;
use File;


class TripController extends Controller
{
     /**********************
     *     helper
     * ********************
     * */

     // response  trip
     protected  function reponseTrip($trip)
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
         $res['publisher']      = $this->responseUser($trip->publisher);
         $res['created_at']     = $trip->created_at->format('d-m-Y h:i a');
         if($trip->status == 1)
         {
             $res['desc']       = is_null($trip->desc)? "":$trip->desc;
             if($trip->privacy)
                $res['privacy'] =  is_null($trip->privacy)? "":$trip->privacy;
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
        $image                  = is_null($user['image'])? "default_image.png" : $user['image'];
        $res['image']           = asset('uploads/publishers') . '/' . $image;
        $res['type']            = $type;
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
    protected function  responsePublishing($publishing){
         $res['id']         = $publishing->id;
         $res['status']     = $publishing->status;
         $res['privacy']    = $publishing->privacy;
         $res['trip']       = $this->reponseTrip($publishing->trip);
         $res['sharer']     = "";
         $res['created_at'] = $publishing->created_at->format('d-m-Y h:i a');

         if($publishing->sharer_id)
         {
             $res['sharer']     = $this->responseUser($publishing->sharer);
         }
        return $res;
    }
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
            'map_screen_shot'     => 'nullable|image',
            'distance'            => 'required',
            'estimated_duration'  => 'required|min:2|max:190',
            'publisher_id'        => 'required|exists:publishers,id',

        ]);
        if ($validator->passes()) {
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
                $photo=$request->map_screen_shot;
                $name = date('d-m-y').time().rand().'.'.$photo->getClientOriginalExtension();
                Image::make($photo)->save('uploads/stores/'.$name);
                $trip->map_screen_shot = $name;

            }
            $trip->save();
            $msg = $request['lang'] == 'ar' ? ' تم بدء الرحله بنجاح.' : ' sucessfull start trip.';
            return response()->json(
                [
                    'status' => true,
                    'data' => ['id'=>$trip->id],
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
                $data  = $this->reponseTrip($trip);
            }
            return response()->json(
                [
                    'status' => true,
                    'data'  => ['trip'=>$data],
                    'msg'   =>""
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


    // finsh trip
    public function endTrip(Request $request){
        $validator=Validator::make($request->all(),[
            'trip_id'        => 'required|exists:trips,id',
            'publisher_id'   => 'required|exists:publishers,id',
            'desc'           => 'nullable',
            'privacy'        => 'required|in:public,private,flowers',

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
                           'data' => "",
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
               $publishing->status       = 1;
               $publishing->publisher_id = $request['publisher_id'];
               $publishing->privacy      = $request['privacy'];
               $publishing->save();
               $msg = $request['lang'] == 'ar' ? ' تم الانهاء بنجاح.' : ' sucessfull  finsh .';
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


    // upload resource for trips
    public  function uploadResource(Request $request){
        $validator=Validator::make($request->all(),[
            'trip_id'        => 'required|exists:trips,id',
            'type'           => 'required|in:vedio,image',
            'desc'           => 'nullable',
            'resource'       => 'required|max:30000000',
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
                $name     = date('d-m-y').time().rand().'.'.$vedio->getClientOriginalExtension();
                $vedio->move(public_path('uploads/tripResources'), $name);
            }
            $resource->resource = $name;
            $resource->save();
            $msg = $request['lang'] == 'ar' ? ' تم الاضافه بنجاح.' : ' sucessfull upload .';
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
        if ($validator->passes()) {
            $trip                = Trip::where('id',$request['trip_id'])
                                        ->where('publisher_id', $request['user_id'])->first();
            if($trip){
                $data  =  $trip->resources->map(function ($resource){
                   return $this->responseResource($resource);
                });
                return response()->json(
                    [
                        'status' => true,
                        'data'   => $data,
                        'msg'    =>""
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
                $publishing               = new Publishing;
                $publishing->trip_id      = $trip->id;
                $publishing->status       = 1;
                $publishing->type         = "share";
                $publishing->publisher_id = $request['publisher_id'];
                $publishing->privacy      = $request['privacy'];
                $publishing->sharer_id    = $request['sharer_id'];
                $publishing->save();
                $msg = $request['lang'] == 'ar' ? ' تم المشاركه بنجاح.' : 'sucessfull sharing.';
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
            $publishing                = Publishing::where('id',$request['publishing_id'])
                ->where('publisher_id', $request['user_id'])
                ->orWhere('sharer_id',$request['user_id'])->first();
            if($publishing){

                return response()->json(
                    [
                        'status' => true,
                        'data' => ['publishing'=> $this->responsePublishing($publishing)],
                        'msg'=>""
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


}

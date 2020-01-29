<?php

namespace App\Http\Controllers\API;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Trip;
use App\Models\TripResource;
use App\Models\Publishing;
use App\Models\Comment;
use App\Models\Favourite;
use App\Models\Favourit;
use App\Models\Like;
use App\Models\Block;
use App\Models\Follower;
use App\Models\LogActivity;
use App\Models\Country;
use App\Models\City;
use App\Models\Subcity;
use App\Models\Locality;
use URL;
use App\Models\Publisher;
use App\Models\StorePlace;
use Image;
use Validator;
use File;
use App\Models\Spot;
use App\Models\Journal;
use App\Models\File as Files;
use App\Models\Tag;
use App\Http\Resources\TagResource;
use App\Http\Resources\JournalResource;
use App\Http\Resources\SpotResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\FollowsResource;
use App\Http\Resources\FavouritesResource;
use App\Http\Resources\CommentsResource;
use App\Http\Resources\GetSpotsCountResource;
use App\Http\Resources\GetSubcitiesSpotsCount;
use App\Http\Resources\GetCitiesSpotsCount;
use App\Http\Resources\PublisherSearchResource;
use App\Http\Resources\CountrySeachResource;
use App\Http\Resources\CitySeachResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use App\Notifications\Comment as commentNotify;
use App\Notifications\Like as LikeNotify;
use App\Notifications\ShareNotification;

class TripController extends Controller
{
    use ApiResponseTrait;

    /**********************
     *     helper
     * ********************
     * */

    // response  trip
    protected function reponseTrip($trip, $publisher = true)
    {
        $res['id'] = $trip->id;
        $res['start_lat'] = $trip->start_lat;
        $res['end_lat'] = $trip->end_lat;
        $res['start_lng'] = $trip->start_lng;
        $res['end_lng'] = $trip->end_lng;
        $res['start_address'] = $trip->start_address;
        $res['end_address'] = $trip->end_address;
        $image = is_null($trip['map_screen_shot']) ? "default_image.png" : $trip['map_screen_shot'];
        $res['map_screen_shot'] = asset('uploads/trips') . '/' . $image;
        $res['status'] = is_null($trip['status']) ? 0 : $trip['status'];
        $res['distance'] = $trip['distance'];
        $res['estimated_duration'] = $trip['estimated_duration'];
//         if($publisher)
        $res['publisher'] = $this->responseUser($trip->publisher);
//         else
//             $res['publisher_id']  = $trip->publisher_id;
        $res['created_at'] = strtotime($trip->created_at) * 1000;

        if ($trip->status == 1) {
            $res['desc'] = is_null($trip->desc) ? "" : $trip->desc;
            $res['ended_at'] = strtotime(empty($trip->ended_at) ? " " : $trip->ended_at) * 1000;

//             $interval          = $trip->created_at->diff($trip->ended_at);
//             $duration          = [
//                 'day'        => $interval->format('%a'),
//                 'month'     => $interval->format('%m'),
//                 'hours'     => $interval->format('%h'),
//                 'minutes'   => $interval->format('%i'),
//                 'years'     => $interval->format('%y')
//             ];
//             $res['duration']= $duration;
        }

        return $res;
    }

    // response user
    protected function responseUser($user, $type = 1)
    {
        $res["id"] = $user->id;
        $res["username"] = $user->username;
        $res["display_name"] = $user->display_name;
        $image = is_null($user['image']) ? "default_image.png" : $user['image'];
        $res['image'] = asset('uploads/publishers') . '/' . $image;
        $res['type'] = $type;
        return $res;
    }

    protected function responseUserProfile($user, $userSeen_id)
    {

        $res["id"] = $user->id;
        $res["username"] = $user->username;
        $res["display_name"] = $user->display_name;
        $res["mobile"] = $user->mobile;
        $res['trips_count'] = Publishing::where('publisher_id', $user->id)->orWhere('sharer_id', $user->id)->count();
        $res["email"] = $user->email;
        $res["city"] = $user->city;
        $res['bio'] = is_null($user['bio']) ? "" : $user['bio'];
        $image = is_null($user['image']) ? "default_image.png" : $user['image'];
        $res['image'] = asset('uploads/publishers') . '/' . $image;
        $res['status'] = is_null($user['status']) ? 1 : $user['status'];
        $res['verified'] = is_null($user['verified']) ? 1 : $user['verified'];
        $res['follow_status'] = $user->follows()->where('follower_id', $userSeen_id)->count() > 0;
        $res['block_status'] = $user->blockeds()->where('user_id', $userSeen_id)->count() > 0;
        $res['follower'] = $user->follows()->count();
        $res['follow'] = $user->followers()->count();
        $res['type'] = 1;
        $res['notification_count'] = $user->unreadNotifications()->count();
        return $res;
    }

    // response the resource
    protected function responseResource($resource)
    {
        $res['id'] = $resource['id'];
        $res['type'] = $resource['type'];
        $res['resource'] = asset('uploads/tripResources/' . $resource->resource);
        $res['lat'] = $resource['lat'];
        $res['lng'] = $resource['lng'];
        $res['address'] = $resource['address'];
        $res['desc'] = is_null($resource['desc']) ? "" : $resource['desc'];
        $res['created_at'] = $resource->created_at->format('d-m-Y h:i a');
        return $res;
    }

    // delete resoruece image
    protected function responsePublishing($publishing, $user_id = 0)
    {
        $res['id'] = $publishing->id;
        $res['status'] = $publishing->status;
        $res['block'] = $publishing->block;
        $res['privacy'] = $publishing->privacy;
        $res['postTrip'] = $this->reponseTrip($publishing->trip);
        $res['isshare'] = false;

        $res['faves_count'] = Favourit::where('publishing_id', $publishing->id)->count();


        $res['res_status'] = TripResource::where('trip_id', $publishing->trip_id)->count() > 0;
        $res['res_count'] = TripResource::where('trip_id', $publishing->trip_id)->count();


        $res['comments'] = $publishing->comments->count();
        $res['likes_count'] = $publishing->likes->count();
        $res['likes_latest'] = $res['likes_count'] > 0 ? $publishing->likes()->latest()->first()->user->display_name : "";
        $res['share_count'] = Publishing::shareTrips($publishing->trip_id)->count();
        if ($res['share_count'] > 0)
            $res['share_latest'] = Publishing::shareTrips($publishing->trip_id)
                ->latest()->first()->sharer->dispaly_name;
        else
            $res['share_latest'] = "";
        if ($publishing->sharer_id) {
            $res['sharer'] = $this->responseUser($publishing->sharer);
            $res['isshare'] = true;
        }
        if ($user_id) {
            $res['likes_status'] = $publishing->likes()->where('user_id', $user_id)->count() > 0;
            $res['fav_status'] = $publishing->favourits()->where('user_id', $user_id)->count() > 0;
        }
        $res['created_at'] = strtotime($publishing->created_at) * 1000;

        return $res;
    }

    // response comment
    protected function responseComment($comment, $user_id = 0)
    {
        $res['id'] = $comment->id;
        $res['body'] = $comment->body;
        $res['user'] = $this->responseUser($comment->user);
        $res['publishing_id'] = $comment->publishing_id;
        if ($user_id)
            $res['status'] = $comment->user_id == $user_id;
        $res['created_at'] = strtotime($comment->created_at) * 1000;

        return $res;
    }

    // response share-user

    protected function responseShareUser($share)
    {
        $res['publishing_id'] = $share['id'];
        $res['user'] = $this->responseUser($share->user);
        $res['created_at'] = $share->created_at->format('d-m-Y h:i a');
        return $res;
    }

    // response like
    protected function responseLike($like)
    {
        $res['id'] = $like['id'];
        $res['user'] = $this->responseUser($like->User);
        $res['publishing_id'] = $like->publishing_id;
        $res['created_at'] = $like->created_at->format('d-m-Y h:i a');
        return $res;
    }


    // save logs
    public  function save_log($publisher_id, $action_id, $type, $status)
    {
        $log = new LogActivity;
        $log->publisher_id = $publisher_id;
        $log->action_id = $action_id;
        $log->type = $type;
        $log->status = $status;

        $log->save();
    }

    //=====================================================================
    //response storeType
    protected function responseStoreType($storeType)
    {
        $res['name_ar'] = $storeType['name_ar'];
        $res['name_en'] = $storeType['name_en'];
        $res['icon'] = asset('uploads/storeTypes') . '/' . $storeType->icon;
        return $res;

    }

    // rpsonse place
    protected function responseStorePlace($storePlace)
    {
        $res['lat'] = $storePlace['lat'];
        $res['lng'] = $storePlace['lng'];
        $res['address'] = $storePlace['address'];
        $res['desc'] = $storePlace['desc'];
        $res['image'] = asset('uploads/storePlace') . '/' . $storePlace->image;
        return $res;

    }

    // reponse store
    protected function responseUserStore($user, $type = 2)
    {
        $res["id"] = $user->id;
        $res["store_name"] = $user->store_name;
        $res["mobile"] = $user->mobile;
        $res["email"] = $user->email;
        $res["city"] = $user->city;
        $image = is_null($user['image']) ? "default_image.png" : $user['image'];
        $res['image'] = asset('uploads/publishers') . '/' . $image;
        $res['status'] = is_null($user['status']) ? 1 : $user['status'];
        $res['verified'] = is_null($user['verified']) ? 1 : $user['verified'];
        $res['type'] = $type;
        $res['storeType'] = $this->responseStoreType($user->StoreType);
        return $res;
    }

    // repsonse near
    protected function responsNear($place)
    {
        $res['places'] = $this->responseStorePlace($place);
        $res['store'] = $this->responseUserStore($place->store);
        return $res;
    }

    protected function correctImageOrientation($filename) {
        if (function_exists('exif_read_data')) {
            $exif = exif_read_data($filename);
            if($exif && isset($exif['Orientation'])) {
                $orientation = $exif['Orientation'];
                if($orientation != 1){
                    $img = imagecreatefromjpeg($filename);
                    $deg = 0;
                    switch ($orientation) {
                        case 3:
                            $deg = 180;
                            break;
                        case 6:
                            $deg = 270;
                            break;
                        case 8:
                            $deg = 90;
                            break;
                    }
                    if ($deg) {
                        $img = imagerotate($img, $deg, 0);
                    }
                    // then rewrite the rotated image back to the disk as $filename
                    imagejpeg($img, $filename, 95);
                } // if there is some rotation necessary
            } // if have the exif orientation info
        } // if function exists
    }
    //============================================================

    /*================================*/
    // start trip
    public function startTrip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "start_lat" => 'required',
            'start_lng' => 'required',
            'start_address' => 'required|min:2|max:190',
            'end_lat' => 'required',
            'end_lng' => 'required',
            'end_address' => 'required|min:2|max:190',
            'map_screen_shot' => 'nullable',
            'distance' => 'required',
            'estimated_duration' => 'required|min:2|max:190',
            'publisher_id' => 'required|exists:publishers,id',

        ]);
        if ($validator->passes()) {
            $check = Trip::where('publisher_id', $request['publisher_id'])
                ->where('status', 0)->first();
            if ($check) {
                $msg = $request['lang'] == 'ar' ? ' لايمكن بدء رحله قبل انتهاء من الرحله القديمه.' : ' you can\'t start trip befor finsh the current.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => ['trip' => ['id' => null]],
                        'msg' => $msg
                    ]
                );
            }
            $trip = new Trip;
            $trip->start_lat = $request['start_lat'];
            $trip->start_lng = $request['start_lng'];
            $trip->start_address = $request['start_address'];
            $trip->end_lat = $request['end_lat'];
            $trip->end_lng = $request['end_lng'];
            $trip->end_address = $request['end_address'];
            $trip->distance = $request['distance'];
            $trip->estimated_duration = $request['estimated_duration'];
            $trip->publisher_id = $request['publisher_id'];
            if ($request['map_screen_shot']) {
                $name = upload_img($request['map_screen_shot'], public_path('uploads/trips/'));
                $trip->map_screen_shot = $name;

            }
            $trip->save();
            $date = $this->reponseTrip($trip);

            publisher_log(
                $request['publisher_id'],
                'لقد قمت ببدء رحله',
                'you start trip'
            );


            $msg = $request['lang'] == 'ar' ? ' تم بدء الرحله بنجاح.' : ' sucessfull start trip.';
            return response()->json(
                [
                    'status' => true,
                    'data' => ['trip' => $date],
                    'msg' => $msg
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

    // home trip
    public function currentTrip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'publisher_id' => 'required|exists:publishers,id',
        ]);
        if ($validator->passes()) {
            $trip = Trip::where('publisher_id', $request['publisher_id'])
                ->where('status', 0)->first();
            $data = [];
            if ($trip) {
                $data = $this->reponseTrip($trip, false);
                $near_start = get_near_stores($trip->start_lat, $trip->start_lng, 10);
                $near_end = get_near_stores($trip->end_lat, $trip->end_lng, 10);
                $all = array_merge($near_end, $near_start);
                $storePlaces = StorePlace::whereIn('id', $all)->get();
                $dataPlace = $storePlaces->map(function ($place) {
                    return $this->responsNear($place);
                });

                $res['trip'] = $data;
                $res['nearStore'] = $dataPlace;

                return response()->json(
                    [
                        'status' => true,
                        'data' => ['currentTrip' => $res],
                        'msg' => ""
                    ]
                );
            } else {
                return response()->json(
                    [
                        'status' => false,
                        'data' => ['id' => null],
                        'msg' => ""
                    ]
                );
            }

        } else {
            foreach ((array)$validator->errors() as $key => $value) {
                foreach ($value as $msg) {
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // get trip

    public function getTrip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required|exists:trips,id'
        ]);
        if ($validator->passes()) {
            $trip = Trip::where('id', $request['trip_id'])
                ->where('status', 1)->first();
            $data = [];
            if ($trip) {
                $data = $this->reponseTrip($trip);
                return response()->json(
                    [
                        'status' => true,
                        'data' => ['ended_trip' => $data],
                        'msg' => ""
                    ]
                );
            } else {
                return response()->json(
                    [
                        'status' => false,
                        'data' => ['trip' => $data],
                        'msg' => ""
                    ]
                );
            }

        } else {
            foreach ((array)$validator->errors() as $key => $value) {
                foreach ($value as $msg) {
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // public function getTrips
    // finsh trip
    public function endTrip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required|exists:trips,id',
            'publisher_id' => 'required|exists:publishers,id',
            'desc' => 'nullable',
            'privacy' => 'required|in:public,private,flowers',
            'status' => 'required|in:0,1,2' // 0 for not publsihin now  1 publishing now ,2for cancel

        ]);
        if ($validator->passes()) {

            $trip = Trip::where('id', $request['trip_id'])
                ->where('publisher_id', $request['publisher_id'])->first();
            if ($trip) {
                /*  if ($trip->status) {
                      $msg = $request['lang'] == 'ar' ? ' هذه الرحله تم الانهاء منها.' : ' this trip already finshed.';
                      return response()->json(
                          [
                              'status' => false,
                              'data' => ['trip' => ['id' => null]],
                              'msg' => $msg
                          ]
                      );
                  }*/
                $trip->status = 1;
                $trip->desc = $request['desc'];
                $trip->ended_at = Carbon::now();
                $trip->update();
                $publishing = new Publishing;
                $publishing->trip_id = $trip->id;
                $publishing->status = $request['status'];
                $publishing->publisher_id = $request['publisher_id'];
                $publishing->privacy = $request['privacy'];
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
                        'data' => ['trip' => ['id' => null]],
                        'msg' => $msg
                    ]
                );

            } else {
                $msg = $request['lang'] == 'ar' ? ' المستخدم لايملك هذه الرحله.' : ' user not owner the trip.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => ['trip' => ['id' => null]],
                        'msg' => $msg
                    ]
                );
            }
        } else {
            foreach ((array)$validator->errors() as $key => $value) {
                foreach ($value as $msg) {
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }


    // upload resource for trips
    public function uploadResource(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'trip_id' => 'required|exists:trips,id',
            'type' => 'required|in:vedio,image',
            'desc' => 'nullable',
            'resource' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'address' => 'required'

        ]);
        if ($validator->passes()) {
            $trip = Trip::find($request['trip_id']);
            $resource = new TripResource;
            $resource->type = $request['type'];
            $resource->desc = $request['desc'];
            $resource->trip_id = $trip->id;
            $resource->lat = $request['lat'];
            $resource->lng = $request['lng'];
            $resource->address = $request['address'];
            if ($request['type'] == "image") {
                $photo = $request->resource;
                $name = date('d-m-y') . time() . rand() . '.' . $photo->getClientOriginalExtension();
//                Image::make($photo)->resize(800, 500)->save('uploads/tripResources/'.$name);
                $photo->move(public_path('uploads/tripResources'), $name);
            } else {
                $vedio = $request->resource;
                $name = date('d-m-y') . time() . rand() . '.' . $vedio->getClientOriginalExtension();
                $vedio->move(public_path('uploads/tripResources'), $name);
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
                    'data' => ['resource' => ["id" => null]],
                    'msg' => $msg
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

    public function createSpot(Request $request)
    {

        $validatedData = Validator::make($request->all(), [
            'publisher_id' => 'required|numeric|exists:publishers,id',
            'location' => 'required',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'files' => 'required|array',
            'files.*' => 'required|file|max:25000|mimes:jpeg,png,jpg,gif,svg,mpeg,ogg,mp4,webm,3gp,mov,flv,avi,wmv,ts',
            'tags' => 'max:255'
        ]);



        if ($validatedData->passes()) {
//            dd($request->tags);
            if ($request->country_name ) {
                $country = Country::where('name', $request->country_name )->first();
                if (!$country) {
                    $country = Country::create(['name' => $request->country_name]);
                }
            }

            if ($request->admin_area   ) {
                $city = City::where('name', $request->admin_area   )->first();
                if (!$city) {
                    $city = City::create(['name' => $request->admin_area   ]);
                }
            }

            if ($request->sub_adminArea) {
                $subcity = Subcity::where('name', $request->sub_adminArea)->first();
                if (!$subcity) {
                    $subcity = Subcity::create(['name' => $request->sub_adminArea]);
                }
            }

            if ($request->locality) {
                $locality = Locality::where('name', $request->locality)->first();
                if (!$locality) {
                    $locality = Locality::create(['name' => $request->locality]);
                }
            }


            $spot = Spot::create([
                'title' => $request->place_name,
                'publisher_id' => $request->publisher_id,
                'location' => $request->location,
                'desc' => $request->desc,
                'lat' => $request->lat,
                'lng' => $request->lng,
                'country_id' => isset($country)  ? $country->id : null,
                'city_id' => isset($city) ? $city->id : null,
                'subcity_id' => isset($subcity) ? $subcity->id : null,
                'locality_id' => isset($locality) ? $locality->id : null,
                'journal_id' => !is_numeric($request->journal_id) ? null : $request->journal_id,
                'status' => 1,

            ]);

            $imageExtensions = ['jpg', 'jpeg', 'gif', 'png', 'PNG', 'bmp', 'svg'];
            $videoExtensions = ['mpeg', 'ogg', 'mp4', 'webm', '3gp', 'mov', 'flv', 'avi', 'wmv', 'ts'];

            if($request->has('files')) {
                foreach ($request->file('files') as $file) {

                    if (in_array($file->getClientOriginalExtension(), $imageExtensions)) {

                        $basename = Str::random();
                        $original = $basename . "." . $file->getClientOriginalExtension();

                        $file->move("uploads/spots", $original);

                        $spot->files()->create([
                            "file" => "uploads/spots/" . $original,
                            "type" => "image",
                            "spot_id" => $spot->id,
                        ]);


                    } else if (in_array($file->getClientOriginalExtension(), $videoExtensions)) {

                        $basename = Str::random();
                        $original = $basename . "." . $file->getClientOriginalExtension();
                        $file->move("uploads/spots", $original);

                        $spot->files()->create([
                            "file" => "uploads/spots/" . $original,
                            "type" => "video",
                            "spot_id" => $spot->id,
                        ]);
                    }
                }

            }


            if ($request->tags) {
//                $tags = explode(" ", $request->tags);
                foreach ($request->tags as $tag) {
                    $tag_found = Tag::where('name', $tag)->first();
                    if (!$tag_found) {
                        $newTag = Tag::create([
                            'name' => $tag
                        ]);
                        $spot->tags()->attach($newTag->id);
                    } else {
                        $spot->tags()->attach($tag_found->id);
                    }
                }
            }


            $msg = 'Spot created successfully.';

            return $this->apiResponse(null, $msg , 201);

        } else if($validatedData->fails()){

            return $this->apiResponse(null , $validatedData->errors()->first(), 400);

        }

    }

    public function updateSpot(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'publisher_id' => 'required|numeric|exists:publishers,id',
            'spot_id' => 'required|numeric|exists:spots,id',
            'lat' => 'numeric',
            'lng' => 'numeric',
//            'journal_id' => 'numeric|exists:journals,id',
            'files' => 'array',
            'files.*' => 'file|max:25000|mimes:jpeg,png,jpg,gif,svg,mpeg,ogg,mp4,webm,3gp,mov,flv,avi,wmv,ts',
            'tags' => 'max:255',

        ]);

        $imageExtensions = ['jpg', 'jpeg', 'gif', 'png', 'PNG', 'bmp', 'svg'];
        $videoExtensions = ['mpeg', 'ogg', 'mp4', 'webm', '3gp', 'mov', 'flv', 'avi', 'wmv', 'ts'];

        if ($validatedData->passes()) {

            $spot = Spot::findOrfail($request->spot_id);

            if ($spot->publisher_id == $request->publisher_id){

                $spot->update([
                    'title' => $request->place_name,
                    'publisher_id' => $request->publisher_id,
                    'location' => $request->location,
                    'desc' => $request->desc,
                    'lat' => $request->lat,
                    'lng' => $request->lng,
                    'journal_id' => !is_numeric($request->journey_id) ? null : $request->journey_id,
                    'status' => 1,

                ]);

                if($request->file('files')) {
                    foreach ($request->file('files') as $file) {

                        if (in_array($file->getClientOriginalExtension(), $imageExtensions)) {

                            $basename = Str::random();
                            $original = $basename . "." . $file->getClientOriginalExtension();
                            $file->move("uploads/spots", $original);


                            //logic update file

                            $files = $spot->files;

                            foreach($files as $file) {
                                $file->delete();
                            }

                            Files::create([
                                "file" => "uploads/spots/" . $original,
                                "type" => "image",
                                "spot_id" => $spot->id,
                            ]);



                        } else if (in_array($file->getClientOriginalExtension(), $videoExtensions)) {

                            $basename = Str::random();
                            $original = $basename . "." . $file->getClientOriginalExtension();
                            $file->move("uploads/spots", $original);

                            $files = $spot->files;

                            foreach($files as $file) {
                                $file->delete();
                            }

                            Files::create([
                                "file" => "uploads/spots/" . $original,
                                "type" => "video",
                                "spot_id" => $spot->id,
                            ]);


                        }
                    }

                }

                if ($request->tags) {
//                $tags = explode(" ", $request->tags);
                    foreach ($request->tags as $tag) {
                        $tag_found = Tag::where('name', $tag)->first();
                        if (!$tag_found) {
                            $newTag = Tag::create([
                                'name' => $tag
                            ]);
                            $spot->tags()->attach($newTag->id);
                        } else {
                            $spot->tags()->attach($tag_found->id);
                        }
                    }
                }

                $msg = 'Spot updated successfully.';

                return $this->apiResponse(null, $msg , 201);


            } else {

                $msg = 'Spot not found';

                return $this->apiResponse(null, $msg , 201);
            }

        } else {
            return $this->apiResponse(null , $validatedData->errors()->first(), 400);
        }

    }

    public function deleteSpot(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'publisher_id' => 'required|numeric|exists:publishers,id',
            'spot_id' => 'required|numeric|exists:spots,id',
        ]);

        if ($validatedData->passes()) {

            $spot = Spot::findOrfail($request->spot_id);

            if ($spot->publisher_id == $request->publisher_id) {

                $spot->delete();

                $msg = 'spot deleted successfully';

                return $this->apiResponse(null , $msg, 200);

            } else {

                $msg = 'spot not found';
                return $this->apiResponse(null , $msg, 200);
            }

        } else {
            return $this->apiResponse(null , $validatedData->errors()->first(), 400);
        }
    }

    public function getSpots(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'publisher_id' => 'required|numeric|exists:publishers,id',
        ]);

        if ($validatedData->passes()) {

            $spots = Spot::orderBy('created_at', 'desc')->paginate(20);

            return $this->apiResponse(["Spots" => SpotResource::collection($spots)->foo($request->publisher_id)], null, 200);
        } else {
            return $this->apiResponse(null , $validatedData->errors()->first(), 400);
        }
    }

    public function  getSpot(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'publisher_id' => 'required|numeric|exists:publishers,id',
            'spot_id' => 'required|numeric|exists:spots,id'
        ]);



        $spot = Spot::where("id", $request->spot_id)->get();

        return $this->apiResponse(["Spots" =>  SpotResource::collection($spot)->foo($request->publisher_id)], null , 200);

    }

    public function getSpotsCount(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'zoom' => 'required|numeric',
        ]);

        if ($validatedData->passes()) {

            if ($request->zoom <= 7) {

                $countrySpots = Country::all();

                return $this->apiResponse(["map_spots" => GetSpotsCountResource::collection($countrySpots)], null, 200);

            } else if ($request->zoom > 7 && $request->zoom < 15) {

                if ($request->search) {

                    $search = $request->search;

                    $countryCitySpots = Country::where("name", "LIKE", "%" . $search . "%")->first();
                    $citySubcitiesSpots =  City::where("name", "LIKE", "%" . $search . "%")->first();

                    if ($countryCitySpots) {

                        $countryCitySpots = $countryCitySpots->spots()->whereNotNull('city_id')->get();
                        return $this->apiResponse(["map_spots" => GetCitiesSpotsCount::collection($countryCitySpots)], null, 200);

                    } else if($citySubcitiesSpots) {
                        $citySubcitiesSpots = $citySubcitiesSpots->spots()->whereNotNull('subcity_id')->get();
                        return $this->apiResponse(["map_spots" => GetSubcitiesSpotsCount::collection($citySubcitiesSpots)], null, 200);

                    } else {

                    }
                }

            }


        } else {
            return $this->apiResponse(null , $validatedData->errors()->first(), 400);
        }

    }

    public function SearchedSpots(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'type' => 'required',
            'id' => 'required',
            'publisher_id' => 'required|numeric|exists:publishers,id',
        ]);

        if ($validatedData->passes()) {

            if ($request->type == "country") {
                $country = Country::where("id", $request->id)->first();
                if ($country) {
                    $countrySpots = $country->spots()->paginate(20);
                    return $this->apiResponse(["Spots" => SpotResource::collection($countrySpots)->foo($request->publisher_id)], null, 200);


                }
            } else if ($request->type == "city") {
                $city = City::where("id", $request->id)->first();
                if ($city) {
                    $citySpots = $city->spots()->paginate(20);
                    return $this->apiResponse(["Spots" => SpotResource::collection($citySpots)->foo($request->publisher_id)], null, 200);

                }

            }else if ($request->type == "subcity") {
                $subcity = City::where("id", $request->id)->first();
                if ($subcity) {
                    $subcitySpots = $subcity->spots()->paginate(20);
                    return $this->apiResponse(["Spots" => SpotResource::collection($subcitySpots)->foo($request->publisher_id)], null, 200);

                }
            }


        } else {
            return $this->apiResponse(null , $validatedData->errors()->first(), 400);
        }


    }

    public function multipleSearch(Request $request)
    {
        if ($request->search) {

            $search = $request->search;

            $publishers = Publisher::where("username", "LIKE", "%" . $search . "%")
            ->orWhere("display_name", "LIKE", "%" . $search . "%")->get();

            $country = Country::where("name", "LIKE", "%" . $search . "%")->get();
            $city = City::where("name", "LIKE", "%" . $search . "%")->get();

            return $this->apiResponse([
                "Publishers" => PublisherSearchResource::collection($publishers),
                "Places" => [
                    CountrySeachResource::collection($country),
                    CitySeachResource::collection($city)
                ]

            ], null, 200);

        }
    }



    public function userProfile(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'user_id' => 'required|numeric|exists:publishers,id',
            'publisher_id' => 'required|numeric|exists:publishers,id',
        ]);

        if ($validatedData->passes()) {

            $user = Publisher::findOrfail($request->user_id);

            $spots = $user->spots;

            return $this->apiResponse([
                "Profile" => (new UserResource($user))->publisher($request->publisher_id),
            ], null, 200);

        } else {
            return $this->apiResponse(null , $validatedData->errors()->first(), 400);
        }
    }

    public function getUserSpots(Request $request)
    {
        $validatedData = Validator::make($request->all(), [

            'publisher_id' => 'required|numeric|exists:publishers,id',
        ]);

        if ($validatedData->passes()) {

            $spots = Spot::where("publisher_id", $request->publisher_id)->orderBy("created_at", "desc")->paginate(20);


            return $this->apiResponse([
               "Spots" => SpotResource::collection($spots)->foo($request->publisher_id)
            ], null, 200);

        } else {
            return $this->apiResponse(null , $validatedData->errors()->first(), 400);
        }
    }

    public function likeSpot(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'spot_id' => 'required|numeric|exists:spots,id',
            'publisher_id' => 'required|numeric|exists:publishers,id',
        ]);

        if ($validatedData->passes()) {
            $like = Like::where('spot_id', $request->spot_id)
                ->where('publisher_id', $request->publisher_id)->first();

            if($like) {


                $like->delete();
                $msg = 'like deleted successfully';

            } else {

                Like::create([
                    "publisher_id" => $request->publisher_id,
                    "spot_id" => $request->spot_id
                ]);
                $like = Like::where('spot_id', $request->spot_id)->where('publisher_id', $request->publisher_id)->first();

                $spot = Spot::find($request->spot_id);
                $spot->publisher->notify(new LikeNotify
                (
                    $spot->publisher,
                    "spot",
                    $request->spot_id,
                    $like
                ));

                $msg = 'successfully liked.';
            }

            return $this->apiResponse(null , $msg, 200);
        } else {
            return $this->apiResponse(null , $validatedData->errors()->first(), 400);
        }
    }

    public function addComment(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'spot_id' => 'required|numeric|exists:spots,id',
            'publisher_id' => 'required|numeric|exists:publishers,id',
            'comment' => 'required'
        ]);

        if($validatedData->passes()) {

            $comment = Comment::create([
                "publisher_id" => $request->publisher_id,
                "spot_id" => $request->spot_id,
                "comment" => $request->comment
            ]);

            if (!$comment) {
                $msg = 'unknown error';
                return $this->apiResponse(null , $msg, 200);
            }

            $spot = Spot::find($request->spot_id);
            $spot->publisher->notify(new commentNotify
                (
                    $spot->publisher,
                    "spot",
                    $request->spot_id,
                    $comment
                )
            );


            $msg = 'comment added successfully';
            return $this->apiResponse(null , $msg, 200);

        } else {
            return $this->apiResponse(null , $validatedData->errors()->first(), 400);
        }
    }

    public function updateSpotComment(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'comment_id' => 'required|numeric|exists:comments,id',
            'publisher_id' => 'required|numeric|exists:publishers,id',
            'comment' => 'required',
        ]);

        if ($validatedData->passes()) {

            $comment = Comment::findOrFail($request->comment_id);

            if ($comment->publisher_id == $request->publisher_id) {

                $comment->update([
                    "comment" => $request->comment
                ]);

                $msg = 'comment updated successfully';
                return $this->apiResponse(null , $msg, 200);

            } else {
                $msg = 'comment not found';
                return $this->apiResponse(null , $msg, 200);
            }

        } else {
            return $this->apiResponse(null , $validatedData->errors()->first(), 400);
        }
    }

    public function deleteSpotComment(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'comment_id' => 'required|numeric|exists:comments,id',
            'publisher_id' => 'required|numeric|exists:publishers,id',
        ]);

        if ($validatedData->passes()) {

            $comment = Comment::findOrFail($request->comment_id);

            if ($comment->publisher_id == $request->publisher_id) {

                $comment->delete();

                $msg = 'comment deleted successfully';
                return $this->apiResponse(null , $msg, 200);

            } else {
                $msg = 'comment not found';
                return $this->apiResponse(null , $msg, 200);
            }

        } else {
            return $this->apiResponse(null , $validatedData->errors()->first(), 400);
        }
    }



    public function getSpotComments(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'spot_id' => 'required|numeric|exists:spots,id',
        ]);

        if($validatedData->passes()) {

            $comments = Spot::where("id", $request->spot_id)->first()->comments;

            return $this->apiResponse(["Comments" =>  CommentsResource::collection($comments)], null , 200);


        } else {
            return $this->apiResponse(null , $validatedData->errors()->first(), 400);
        }
    }




    public function addToFavourite(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'spot_id' => 'required|numeric|exists:spots,id',
            'publisher_id' => 'required|numeric|exists:publishers,id',
        ]);

        if ($validatedData->passes()) {
            $favourite = Favourite::where('spot_id', $request->spot_id)
                ->where('publisher_id', $request->publisher_id)->first();

            if($favourite) {


                $favourite->delete();
                $msg = 'spot removed from favourites';

            } else {

                Favourite::create([
                    "publisher_id" => $request->publisher_id,
                    "spot_id" => $request->spot_id
                ]);

                $msg = 'Spot added to favourites successfully';
            }

            return $this->apiResponse(null , $msg, 200);
        } else {
            return $this->apiResponse(null , $validatedData->errors()->first(), 400);
        }
    }



    public function followsSpots(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'publisher_id' => 'required|numeric|exists:publishers,id',
        ]);

        if ($validatedData->passes()) {

            $followers = Follower::buldFolloweId($request->publisher_id);

            $spots = Spot::whereIn('publisher_id', $followers)->orderBy('created_at', 'desc')->paginate(10);

            return $this->apiResponse(["Spots" =>  SpotResource::collection($spots)->foo($request->publisher_id)], null , 200);

        } else {
            return $this->apiResponse(null , $validatedData->errors()->first(), 400);
        }
    }

    public function favouriteSpots(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'publisher_id' => 'required|numeric|exists:publishers,id',
        ]);

        if ($validatedData->passes()) {

            $publisherFavourites = Favourite::buldPublisherId($request->publisher_id);

            $spots = Spot::whereIn('id', $publisherFavourites)->orderBy('created_at', 'desc')->paginate(10);

            return $this->apiResponse(["Spots" =>  SpotResource::collection($spots)->foo($request->publisher_id)], null , 200);

        } else {
            return $this->apiResponse(null , $validatedData->errors()->first(), 400);
        }
    }



    // Search in tags
    public function tagsSearch(Request $request)
    {

        if ($request->input("tag")) {

            $tags = Tag::searched()->get();

            return $this->apiResponse(['Tag' =>  TagResource::collection($tags)]);

        }

    }

    public function createJourney(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'publisher_id' => 'required|numeric|exists:publishers,id',
            'name' => 'required',
        ]);

        if ($validatedData->passes()) {

            $journalCreated  = Journal::create([
                'name' => $request->name,
                "desc" => isset($request->desc) ? $request->desc : null,
                'publisher_id' => $request->publisher_id
            ]);

            $msg = "Journey created successfully";
            return $this->apiResponse(["Journey" => $journalCreated], $msg , 201);

        } else {
            return $this->apiResponse($validatedData->errors(), null , 400);
        }

    }

    public function getJourneys(Request $request)
    {
        $validatedData = Validator::make($request->all(), [
            'publisher_id' => 'required|numeric|exists:publishers,id',
        ]);

        if ($validatedData->passes()) {

            $journals = Journal::where('publisher_id', $request->publisher_id)->orderBy('created_at', 'desc')->get();


            return $this->apiResponse(['Journeys' => JournalResource::collection($journals)], null , 200);

        } else {
            return $this->apiResponse($validatedData->errors(), null , 400);
        }
    }


    // delete resource
    public function deleteResource(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'resource_id' => 'required|exists:trip_resources,id',
        ]);
        if ($validator->passes()) {
            $resource = TripResource::find($request['resource_id']);
            File::delete('uploads/tripResources/' . $resource->resource);
            $resource->delete();
            $msg = $request['lang'] == 'ar' ? ' تم الحذف.' : ' sucessfull delete.';
            return response()->json(
                [
                    'status' => true,
                    'data' => "",
                    'msg' => $msg
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

    // get resoorces trips
    public function getTripResources(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required|exists:trips,id',
            'user_id' => 'required|exists:publishers,id'

        ]);

        $trip = Trip::find($request['trip_id']);
        if ($trip) {
            $data = $trip->resources->map(function ($resource) {
                return $this->responseResource($resource);
            });
            return response()->json(
                [
                    'status' => true,
                    'data' => ['resources' => $data],
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

    //update resourece
    // update basic info trip
    public function updateResource(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'resource_id' => 'required|exists:trip_resources,id',
            'user_id' => 'required|exists:publishers,id',
            'type' => 'required|in:vedio,image',
            'desc' => 'nullable',
            'resource' => 'nullable|max:30000000',
            'lat' => 'required',
            'lng' => 'required',
            'address' => 'required'

        ]);
        if ($validator->passes()) {
            $resource = TripResource::find($request['resource_id']);

            if ($resource->trip->publisher_id == $request['user_id']) {
                $resource->type = $request['type'];
                $resource->desc = $request['desc'];
                $resource->lat = $request['lat'];
                $resource->lng = $request['lng'];
                $resource->address = $request['address'];
                if ($request['resource']) {
                    File::delete('uploads/tripResources/' . $resource->resource);
                    if ($request['type'] == "image") {
                        $photo = $request->resource;
                        $name = date('d-m-y') . time() . rand() . '.' . $photo->getClientOriginalExtension();
                        Image::make($photo)->save('uploads/tripResources/' . $name);
                    } else {
                        $vedio = $request->resource;
                        $name = date('d-m-y') . time() . rand() . '.' . $vedio->getClientOriginalExtension();
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
                        'msg' => $msg
                    ]
                );
            } else {
                $msg = $request['lang'] == 'ar' ? ' المستخدم لايملك هذه الرحله.' : ' user not owner the trip.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg' => $msg
                    ]
                );
            }

        } else {
            foreach ((array)$validator->errors() as $key => $value) {
                foreach ($value as $msg) {
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // delete trip
    public function deleteTrip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required|exists:trips,id',
            'user_id' => 'required|exists:publishers,id'

        ]);
        if ($validator->passes()) {
            $trip = Trip::where('id', $request['trip_id'])
                ->where('publisher_id', $request['user_id'])->first();
            if ($trip) {
                $trip->delete();
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
                        'data' => ['trip' => ['id' => null]],
                        'msg' => $msg
                    ]
                );
            } else {
                $msg = $request['lang'] == 'ar' ? ' المستخدم لايملك هذه الرحله.' : ' user not owner the trip.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => ['trip' => ['id' => null]],
                        'msg' => $msg
                    ]
                );
            }

        } else {
            foreach ((array)$validator->errors() as $key => $value) {
                foreach ($value as $msg) {
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // update basic info trip
    public function updateTrip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required|exists:trips,id',
            'user_id' => 'required|exists:publishers,id',
            "start_lat" => 'required',
            'start_lng' => 'required',
            'start_address' => 'required|min:2|max:190',
            'end_lat' => 'required',
            'end_lng' => 'required',
            'end_address' => 'required|min:2|max:190',
            'map_screen_shot' => 'nullable|image',
            'distance' => 'required',
            'estimated_duration' => 'required|min:2|max:190',
        ]);
        if ($validator->passes()) {
            $trip = Trip::where('id', $request['trip_id'])
                ->where('publisher_id', $request['user_id'])->first();
            if ($trip) {
                $trip->start_lat = $request['start_lat'];
                $trip->start_lng = $request['start_lng'];
                $trip->start_address = $request['start_address'];
                $trip->end_lat = $request['end_lat'];
                $trip->end_lng = $request['end_lng'];
                $trip->end_address = $request['end_address'];
                $trip->distance = $request['distance'];
                $trip->estimated_duration = $request['estimated_duration'];
                if ($request['map_screen_shot']) {
                    File::delete('uploads/trips/' . $trip->map_screen_shot);
                    $photo = $request->map_screen_shot;
                    $name = date('d-m-y') . time() . rand() . '.' . $photo->getClientOriginalExtension();
                    Image::make($photo)->save('uploads/stores/' . $name);
                    $trip->map_screen_shot = $name;

                }
                $trip->update();
                $msg = $request['lang'] == 'ar' ? ' تم التعديل.' : ' update sucessful.';
                return response()->json(
                    [
                        'status' => true,
                        'data' => ["ended_trip" => ['id' => null]],
                        'msg' => $msg
                    ]
                );
            } else {
                $msg = $request['lang'] == 'ar' ? ' المستخدم لايملك هذه الرحله.' : ' user not owner the trip.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg' => $msg
                    ]
                );
            }

        } else {
            foreach ((array)$validator->errors() as $key => $value) {
                foreach ($value as $msg) {
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // change desc
    // update basic info trip
    public function changeDescTrip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required|exists:trips,id',
            'user_id' => 'required|exists:publishers,id',
            'desc' => 'nullable'
        ]);
        if ($validator->passes()) {
            $trip = Trip::where('id', $request['trip_id'])
                ->where('publisher_id', $request['user_id'])->first();
            if ($trip) {
                $trip->desc = $request['desc'];
                $trip->update();
                $msg = $request['lang'] == 'ar' ? ' تم التعديل.' : ' update successful.';
                return response()->json(
                    [
                        'status' => true,
                        'data' => ['trip' => ['id' => null]],
                        'msg' => $msg
                    ]
                );
            } else {
                $msg = $request['lang'] == 'ar' ? ' المستخدم لايملك هذه الرحله.' : ' user not owner the trip.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => ['trip' => ['id' => null]],
                        'msg' => $msg
                    ]
                );
            }

        } else {
            foreach ((array)$validator->errors() as $key => $value) {
                foreach ($value as $msg) {
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
    public function shareTrip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required|exists:trips,id',
            'publisher_id' => 'required|exists:publishers,id',
            'sharer_id' => 'required|exists:publishers,id',
            'privacy' => 'required|in:public,private,flowers',
        ]);
        if ($validator->passes()) {

            $trip = Trip::where('id', $request['trip_id'])
                ->where('publisher_id', $request['publisher_id'])->first();
            if ($trip) {
                // check if share is publisher
                if ($request['publisher_id'] == $request['sharer_id']) {
                    $msg = $request['lang'] == 'ar' ? 'لا يمكن مشاركة منشوراتك.' : 'you can\' share the trip.';
                    return response()->json(
                        [
                            'status' => false,
                            'data' => ['trip' => ['id' => null]],
                            'msg' => $msg
                        ]
                    );
                }
                $check = Publishing::where('trip_id', $trip->id)
                    ->where('sharer_id', $request['sharer_id'])->first();
                // check if the sharer share it before
                if ($check) {
                    $msg = $request['lang'] == 'ar' ? 'لقد قمت بمشاركة الرحله من قبل.' : 'al-ready shared the trip.';
                    return response()->json(
                        [
                            'status' => false,
                            'data' => ['trip' => ['id' => null]],
                            'msg' => $msg
                        ]
                    );
                }
                $publishing = new Publishing;
                $publishing->trip_id = $trip->id;
                $publishing->status = 1;
                $publishing->type = "share";
                $publishing->publisher_id = $request['publisher_id'];
                $publishing->privacy = $request['privacy'];
                $publishing->sharer_id = $request['sharer_id'];
                $publishing->save();
                $msg = $request['lang'] == 'ar' ? ' تم المشاركه بنجاح.' : 'sucessfull sharing.';


                $this->save_log(
                    $request['sharer_id'],
                    $request['trip_id'],
                    'SHARE',
                    'publsihing'
                );

                $publishing->publisher->notify(new ShareNotification($publishing->publisher, $publishing->sharer));
                return response()->json(
                    [
                        'status' => true,
                        'data' => ['trip' => ['id' => null]],
                        'msg' => $msg
                    ]
                );
            } else {
                $msg = $request['lang'] == 'ar' ? ' المستخدم لايملك هذه الرحله.' : ' user not owner the trip.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => ['trip' => ['id' => null]],
                        'msg' => $msg
                    ]
                );
            }
        } else {
            foreach ((array)$validator->errors() as $key => $value) {
                foreach ($value as $msg) {
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // get the post
    public function getPublishing(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'publishing_id' => 'required|exists:publishings,id',
            'user_id' => 'required|exists:publishers,id'

        ]);
        if ($validator->passes()) {
            $publishing = Publishing::find($request['publishing_id']);
            $blocks = Block::buldBlockId($request['user_id']);
            $blockingMe = Block::buldBlockerId($request['user_id']);
            $allBlocks = array_merge($blocks, $blockingMe);
            if (in_array($publishing->publisher_id, $allBlocks) || in_array($publishing->sharer_id, $allBlocks)) {
                $msg = $request['lang'] == 'ar' ? ' ليس لديك صلاحيه للمشاهدة المنشور .' : ' privacy not allow you to show post.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => ['posted-trip' => ['id' => null]],
                        'msg' => $msg
                    ]
                );
            }
            if ($publishing->block == 1) {
                $msg = $request['lang'] == 'ar' ? ' لقد تم حظر هذا المنشور .' : 'this post block.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => ['posted-trip' => ['id' => null]],
                        'msg' => $msg
                    ]
                );
            }
            return response()->json(
                [
                    'status' => true,
                    'data' => ['posted-trip' => $this->responsePublishing($publishing, $request['user_id'])],
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

    // get the share
    public function deleteShare(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'publishing_id' => 'required|exists:publishings,id',
            'user_id' => 'required|exists:publishers,id'
        ]);
        if ($validator->passes()) {
            $publishing = Publishing::where('id', $request['publishing_id'])
                ->where('sharer_id', $request['user_id'])->first();
            if ($publishing) {
                $publishing->delete();
                $msg = $request['lang'] == 'ar' ? ' تم الحذف.' : ' sucessfull delete.';
                return response()->json(
                    [
                        'status' => true,
                        'data' => "",
                        'msg' => $msg
                    ]
                );
            } else {
                $msg = $request['lang'] == 'ar' ? ' المستخدم لايملك هذه الرحله.' : ' user not owner the trip.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg' => $msg
                    ]
                );
            }

        } else {
            foreach ((array)$validator->errors() as $key => $value) {
                foreach ($value as $msg) {
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // chnage privacy

    // get the share
    public function changePrivacy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'publishing_id' => 'required|exists:publishings,id',
            'user_id' => 'required|exists:publishers,id',
            'privacy' => 'required|in:public,private,flowers',
        ]);
        if ($validator->passes()) {
            $publishing = Publishing::where('id', $request['publishing_id'])
                ->where(function ($query) use ($request) {
                    $query->where('publisher_id', $request['user_id'])
                        ->orWhere('sharer_id', $request['user_id']);
                })
//                ->where('publisher_id', $request['user_id'])
//                ->orWhere('sharer_id',$request['user_id'])->first();
                ->first();
            if ($publishing) {
                $publishing->privacy = $request->privacy;
                $publishing->save();
//                dd($request['publishing_id'],$publishing->toArray());
                $msg = $request['lang'] == 'ar' ? ' تم تعديل.' : ' sucessfull update.';
                return response()->json(
                    [
                        'status' => true,
                        'data' => ['trip' => ['id' => null]],
                        'msg' => $msg
                    ]
                );
            } else {
                $msg = $request['lang'] == 'ar' ? ' المستخدم لايملك هذه الرحله.' : ' user not owner the trip.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => ['trip' => ['id' => null]],
                        'msg' => $msg
                    ]
                );
            }

        } else {
            foreach ((array)$validator->errors() as $key => $value) {
                foreach ($value as $msg) {
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // save comment
    public function saveComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'publishing_id' => 'required|exists:publishings,id',
            'user_id' => 'required|exists:publishers,id',
            'body' => 'required',
        ]);

        if ($validator->passes()) {
            $publishing = Publishing::find($request['publishing_id']);
            if ($publishing->privacy != "private") {
                $comment = new Comment;
                $comment->publishing_id = $request['publishing_id'];
                $comment->user_id = $request['user_id'];
                $comment->body = $request['body'];
                $comment->save();


                if ($publishing->sharer_id) {

                    $publishing->sharer->notify(new commentNotify
                        (
                            $publishing->sharer,
                            "publishing",
                            $publishing->id,
                            $comment
                        )
                    );
                } else {
                    $publishing->publisher->notify(new commentNotify
                        (
                            $publishing->publisher,
                            "publishing",
                            $publishing->id,
                            $comment
                        )
                    );
                }


                $this->save_log(
                    $request['user_id'],
                    $request['publishing_id'],
                    'COMMENT',
                    'publsihing'
                );

                $msg = $request['lang'] == 'ar' ? ' تم اضافة التعليق.' : ' sucessfull add comment.';
                return response()->json(
                    [
                        'status' => true,
                        'data' => ["comment" => $this->responseComment($comment, $request['user_id'])],
                        'msg' => $msg
                    ]
                );
            } else {
                $msg = $request['lang'] == 'ar' ? ' ليس لديك صلاحيه للتعليق .' : ' privacy not allow you to comment.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg' => $msg
                    ]
                );
            }

        } else {
            foreach ((array)$validator->errors() as $key => $value) {
                foreach ($value as $msg) {
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // delete comment
    public function deleteComment(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'comment_id' => 'required|exists:comments,id',
            'user_id' => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {
            $comment = Comment::find($request['comment_id']);
            if ($comment->user_id == $request['user_id']) {
                $comment->delete();
                $msg = $request['lang'] == 'ar' ? ' تم حذف التعليق.' : ' sucessfull delete comment.';
                return response()->json(
                    [
                        'status' => true,
                        'data' => ['trip' => ['id' => null]],
                        'msg' => $msg
                    ]
                );
            } else {
                $msg = $request['lang'] == 'ar' ? ' التعليق غير موجود ف تعليقاتك .' : 'comment not found in comments.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => ['trip' => ['id' => null]],
                        'msg' => $msg
                    ]
                );
            }

        } else {
            foreach ((array)$validator->errors() as $key => $value) {
                foreach ($value as $msg) {
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // get comment
    public function getComment(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'comment_id' => 'required|exists:comments,id',
            'user_id' => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {
            $comment = Comment::find($request['comment_id']);
            if ($comment->user_id == $request['user_id']) {
                return response()->json(
                    [
                        'status' => true,
                        'data' => ["comment" => $this->responseComment($comment, $request['user_id'])],
                        'msg' => ""
                    ]
                );
            } else {
                $msg = $request['lang'] == 'ar' ? ' التعليق غير موجود ف تعليقاتك .' : 'comment not found in comments.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => ['comment' => ['id' => null]],
                        'msg' => $msg
                    ]
                );
            }

        } else {
            foreach ((array)$validator->errors() as $key => $value) {
                foreach ($value as $msg) {
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // update comment
    public function updateComment(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'comment_id' => 'required|exists:comments,id',
            'user_id' => 'required|exists:publishers,id',
            'body' => 'required'
        ]);

        if ($validator->passes()) {
            $comment = Comment::find($request['comment_id']);
            if ($comment->user_id == $request['user_id']) {
                $comment->body = $request['body'];
                $comment->save();
                return response()->json(
                    [
                        'status' => true,
                        'data' => ["comment" => $this->responseComment($comment, $request['user_id'])],
                        'msg' => ""
                    ]
                );
            } else {
                $msg = $request['lang'] == 'ar' ? ' التعليق غير موجود ف تعليقاتك .' : 'comment not found in comments.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => ['comment', ['id' => null]],
                        'msg' => $msg
                    ]
                );
            }

        } else {
            foreach ((array)$validator->errors() as $key => $value) {
                foreach ($value as $msg) {
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // get all comment
    public function getComments(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'publishing_id' => 'required|exists:publishings,id',
            'user_id' => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {
            $publishing = Publishing::find($request['publishing_id']);

            // check privacy
            if ($publishing->privacy != "private") {
                $data = $publishing->comments()->latest()->get()->map(function ($comment) use ($request) {
                    return $this->responseComment($comment, $request['user_id']);
                });

                return response()->json(
                    [
                        'status' => true,
                        'data' => ['comments' => $data],
                        'msg' => ""
                    ]
                );
            } else {
                $msg = $request['lang'] == 'ar' ? ' ليس لديك صلاحيه للتعليق .' : ' privacy not allow you to comment.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => ['comments' => ['id' => null]],
                        'msg' => $msg
                    ]
                );
            }


        } else {
            foreach ((array)$validator->errors() as $key => $value) {
                foreach ($value as $msg) {
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }


    // likes action delete and add
    public function likeAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'publishing_id' => 'required|exists:publishings,id',
            'user_id' => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {
            $publishing = Publishing::find($request['publishing_id']);
            if ($publishing->privacy != "private") {
                $like = Like::where('publishing_id', $request['publishing_id'])
                    ->where('user_id', $request['user_id'])->first();
                if ($like) {
                    $msg = $request['lang'] == 'ar' ? ' تم حذف الاعجاب.' : ' sucessfull delete liked.';

                    $this->save_log(
                        $request['user_id'],
                        $request['publishing_id'],
                        'DISLIKE',
                        'publsihing'
                    );
                    $like->delete();
                } else {
                    $like = new Like;
                    $like->user_id = $request['user_id'];
                    $like->publishing_id = $request['publishing_id'];
                    $like->save();
                    $msg = $request['lang'] == 'ar' ? ' تم الاعجاب.' : ' sucessfull liked.';


                    if ($publishing->sharer_id) {
                        $publishing->sharer->notify(new LikeNotify
                            (
                                $publishing->sharer,
                                "publishing",
                                $publishing->id,
                                $like
                            )
                        );
                    } else {
                        $publishing->publisher->notify(new LikeNotify
                            (
                                $publishing->publisher,
                                "publishing",
                                $publishing->id,
                                $like
                            )
                        );
                    }

                    $this->save_log(
                        $request['user_id'],
                        $request['publishing_id'],
                        'LIKE',
                        'publsihing'
                    );
                }

                return response()->json(
                    [
                        'status' => true,
                        'data' => ['trip' => ['id' => null]],
                        'msg' => $msg
                    ]
                );

            } else {
                $msg = $request['lang'] == 'ar' ? ' ليس لديك صلاحيه للتعليق .' : ' privacy not allow you to comment.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => ['trip' => ['id' => null]],
                        'msg' => $msg
                    ]
                );
            }

        } else {
            foreach ((array)$validator->errors() as $key => $value) {
                foreach ($value as $msg) {
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // list like
    public function getLikes(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'publishing_id' => 'required|exists:publishings,id',
            'user_id' => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {
            $publishing = Publishing::find($request['publishing_id']);

            // check privacy
            if ($publishing->privacy != "private") {
                $data = $publishing->likes->map(function ($like) {
                    return $this->responseLike($like);
                });
                return response()->json(
                    [
                        'status' => true,
                        'data' => ['likes' => $data],
                        'msg' => ""
                    ]
                );
            } else {
                $msg = $request['lang'] == 'ar' ? ' ليس لديك صلاحيه للتعليق .' : ' privacy not allow you to comment.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => ['likes' => ['id' => null]],
                        'msg' => $msg
                    ]
                );
            }


        } else {
            foreach ((array)$validator->errors() as $key => $value) {
                foreach ($value as $msg) {
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }


    // list share
    public function getShares(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'publishing_id' => 'required|exists:publishings,id',
            'user_id' => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {
            $publishing = Publishing::find($request['publishing_id']);

            // check privacy
            if ($publishing->privacy != "private") {
                $data = Publishing::shareTrips($publishing->trip_id)->get()->map(function ($share) {
                    return $this->responseShareUser($share);
                });
                return response()->json(
                    [
                        'status' => true,
                        'data' => ['shares' => $data],
                        'msg' => ""
                    ]
                );
            } else {
                $msg = $request['lang'] == 'ar' ? ' ليس لديك صلاحيه للتعليق .' : ' privacy not allow you to comment.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => ['shares' => ['id' => null]],
                        'msg' => $msg
                    ]
                );
            }


        } else {
            foreach ((array)$validator->errors() as $key => $value) {
                foreach ($value as $msg) {
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // favoutit action
    public function favAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'publishing_id' => 'required|exists:publishings,id',
            'user_id' => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {
            $publishing = Publishing::find($request['publishing_id']);
//            if($request['user_id'] != $request['publishing_id'])
//            {
//                $msg = $request['lang'] == 'ar' ? ' لايمكن اضافة منشورك الى المفضله .' : 'you can add his post to your favourti .';
//                return response()->json(
//                    [
//                        'status' => false,
//                        'data' => ['favourite'=>['id'=>null]],
//                        'msg'=>$msg
//                    ]
//                );
//            }
            if ($publishing->privacy != "private") {
                $fav = Favourit::where('publishing_id', $request['publishing_id'])
                    ->where('user_id', $request['user_id'])->first();
                if ($fav) {
                    $msg = $request['lang'] == 'ar' ? ' تم حذف من المفضله.' : ' sucessfull delete from favourit.';
                    $fav->delete();
                    $this->save_log(
                        $request['user_id'],
                        $request['publishing_id'],
                        'UNFAVORITE',
                        'publsihing'
                    );
                } else {
                    $fav = new Favourit;
                    $fav->user_id = $request['user_id'];
                    $fav->publishing_id = $request['publishing_id'];
                    $fav->save();
                    $this->save_log(
                        $request['user_id'],
                        $request['publishing_id'],
                        'FAVORITE',
                        'publsihing'
                    );
                    $msg = $request['lang'] == 'ar' ? ' تم الاضافه الى المفضله.' : ' sucessfull add to favourit.';
                }

                return response()->json(
                    [
                        'status' => true,
                        'data' => ['favourite' => ['id' => null]],
                        'msg' => $msg
                    ]
                );
            } else {
                $msg = $request['lang'] == 'ar' ? ' ليس لديك صلاحيه للتعليق .' : ' privacy not allow you to comment.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => ['favourite' => ['id' => null]],
                        'msg' => $msg
                    ]
                );
            }

        } else {
            foreach ((array)$validator->errors() as $key => $value) {
                foreach ($value as $msg) {
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // get favourits
    public function getFavs(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {
            $publisher = Publisher::find($request['user_id']);

            $favourits = $publisher->favourits()->latest()->paginate(10);
            $meta = getBasicInfoPagantion($favourits);
            $data = getCollectionPagantion($favourits)->map(function ($fav) use ($request) {
                return $this->responsePublishing($fav->publishing, $request['user_id']);
            });
            $res['posted-trips'] = $data;
            $res['meta'] = $meta;
            return response()->json(
                [
                    'status' => true,
                    'data' => ['posts' => $res],
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

    public function ChangeStatusPublisher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required|exists:trips,id',
            'user_id' => 'required|exists:publishers,id',
            'status' => 'required|in:0,1,2',
        ]);
        if ($validator->passes()) {
            $publishing = Publishing::publishTrip($request['trip_id'])
                ->where('publisher_id', $request['user_id'])->first();
            if ($publishing) {
                if ($publishing->status == 0) {
                    $publishing->status = 1;
                    $publishing->update();
                    $msg = $request['lang'] == 'ar' ? ' تم النشر.' : ' successfull publish.';
                    return response()->json(
                        [
                            'status' => true,
                            'data' => ['trip' => ['id' => null]],
                            'msg' => $msg
                        ]
                    );
                } else {
                    $publishing->status = $request['status'];
                    $publishing->update();
                    $msg = $request['lang'] == 'ar' ? ' تم تغير حالة المنشور.' : ' change the status of the trip.';
                    return response()->json(
                        [
                            'status' => true,
                            'data' => ['trip' => ['id' => null]],
                            'msg' => $msg
                        ]
                    );
                }

            } else {
                $msg = $request['lang'] == 'ar' ? ' المستخدم لايملك هذه الرحله او لم يتم الانتهاء منها.' : ' user not owner the trip or not end yet.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => ['trip' => ['id' => null]],
                        'msg' => $msg
                    ]
                );
            }

        } else {
            foreach ((array)$validator->errors() as $key => $value) {
                foreach ($value as $msg) {
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
    public function publicTrip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {
            $blocks = Block::buldBlockId($request['user_id']);
            $blockingMe = Block::buldBlockerId($request['user_id']);

            $allBlocks = array_merge($blocks, $blockingMe);
//            dd($allBlocks);
            $publsihng2 = Publishing::where('privacy', 'public')
                ->where('block', 0)
                ->where('status', 1)
                ->whereNotNull('sharer_id')
                ->whereNotIn('publisher_id', $allBlocks)
                ->whereNotIn('sharer_id', $allBlocks);

            $publsihng = Publishing::where('privacy', 'public')
                ->where('block', 0)
                ->where('status', 1)
                ->whereNull('sharer_id')
                ->whereNotIn('publisher_id', $allBlocks)
                ->union($publsihng2)
                ->latest()
                ->simplePaginate(10);

            $meta = getBasicInfoPagantion($publsihng);

            $data = getCollectionPagantion($publsihng)->map(function ($publish) use ($request) {
                return $this->responsePublishing($publish, $request['user_id']);
            });
            $res['posted-trips'] = $data;
            $res['meta'] = $meta;
            return response()->json(
                [
                    'status' => true,
                    'data' => ['posts' => $res],
                    'msg' => "",
//                    'meta'   => $meta
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

    // private trip

    // public trip
    public function followerTrip(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {
            $followers = Follower::buldFolloweId($request['user_id']);

            $publsihng2 = Publishing::whereIn('privacy', ['flowers', 'public'])
                ->where('block', 0)
                ->where('status', 1)
                ->whereNotNull('sharer_id')
                ->whereIn('sharer_id', $followers);
            $publsihng = Publishing::whereIn('privacy', ['flowers', 'public'])
                ->where('status', 1)
                ->where('block', 0)
                ->whereNull('sharer_id')
                ->whereIn('publisher_id', $followers)
                ->union($publsihng2)
                ->latest()
                ->simplePaginate(10);
            $meta = getBasicInfoPagantion($publsihng);
            $data = getCollectionPagantion($publsihng)->map(function ($publishing) use ($request) {
                return $this->responsePublishing($publishing, $request['user_id']);
            });
            return response()->json(
                [
                    'status' => true,
                    'data' => ['posts' => ['posted-trips' => $data, 'meta' => $meta]],
                    'msg' => "",
//                    'meta'   => $meta
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

    // get the user profile
    public function publisherProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:publishers,id',
            'publisher_id' => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {

            $followers = Follower::buldFollowerId($request['publisher_id']);
            if ($request['publisher_id'] == $request['user_id']) {
                $publsihng = Publishing::whereIn('privacy', ['public', 'private', 'flowers'])
                    ->where('block', 0)
                    ->where('status', 1)
                    ->where('publisher_id', $request['publisher_id'])
                    ->orWhere('sharer_id', $request['publisher_id'])
                    ->latest()
                    ->simplePaginate(10);
            } else if (in_array($request['user_id'], $followers)) {

                $publsihng = Publishing::whereIn('privacy', ['public', 'flowers'])
                    ->where('block', 0)
                    ->where('status', 1)
                    ->where('publisher_id', $request['publisher_id'])
                    ->orWhere('sharer_id', $request['publisher_id'])
                    ->latest()
                    ->simplePaginate(10);
            } else {
                $publsihng = Publishing::where('privacy', 'public')
                    ->where('publisher_id', $request['publisher_id'])
                    ->orWhere('sharer_id', $request['publisher_id'])
                    ->latest()
                    ->simplePaginate(10);
            }
            $meta = getBasicInfoPagantion($publsihng);
            $publisher = Publisher::find($request['publisher_id']);
            $arr['publisher'] = $this->responseUserProfile($publisher, $request['user_id']);
            $data = getCollectionPagantion($publsihng)->map(function ($publishing) use ($request) {
                return $this->responsePublishing($publishing, $request['user_id']);
            });
            $arr['posted-trips'] = $data;
            $arr['meta'] = $meta;
            return response()->json(
                [
                    'status' => true,
                    'data' => ['profile' => $arr],
                    'msg' => "",
//                    'meta'   => $meta
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

    // get the post
    public function getPublishingUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:publishers,id'

        ]);
        if ($validator->passes()) {
            $publishing = Publishing::whereNull("sharer_id")
                ->where('publisher_id', $request['user_id'])->latest()->simplePaginate(10);

            $meta = getBasicInfoPagantion($publishing);

            $data = getCollectionPagantion($publishing)->map(function ($publishing) use ($request) {
                return $this->responsePublishing($publishing, $request['user_id']);
            });
            return response()->json(
                [
                    'status' => true,
                    'data' => ['posts' => ['posted-trips' => $data, 'meta' => $meta]],
                    'msg' => "",
//                    'meta'   => $meta
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

    // test
    public function test(Request $request)
    {
        /* // Create a client with a base URI
         phpInfo();
         $origin = "30.044281,31.340002";
         $destination = "31.037933,31.381523";
         $client = new Client([
 //            'base_uri' => 'https://foo.com/api/',
         ]);
         $response = $client->get("https://maps.googleapis.com/maps/api/directions/json?origin=" . $origin . "&destination=" . $destination . "&waypoints=&mode=driving&language=ar&key=AIzaSyDYjCVA8YFhqN2pGiW4I8BCwhlxThs1Lc0");;
         $places = json_decode($response->getBody()->getContents())->routes[0]->legs[0]->steps;
         dd(count($places));*/



        echo "teest";
    }






}

<?php

/*******************************
 *   helper
 *  @auhtor Safwat
 * ********************/

// convert to english number
function convert2english($string) {
    $newNumbers = range(0, 9);
    $arabic = array('٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩');
    $string =  str_replace($arabic, $newNumbers, $string);
    return $string;
}

// delete trips image and resource
function deleteTrip($trip)
{
    \File::delete('uploads/trips/'.$trip->map_screen_shot);
    foreach ($trip->resources as $resource){
        \File::delete('uploads/tripResources/'.$resource->resource);
    }
}

// base 64 save
function upload_img($base64_img ,$path) {
    $file     = base64_decode($base64_img);
    $safeName = date('d-m-y').time().rand() . '.' . 'png';
    file_put_contents($path . $safeName, $file);
    return $safeName;
}

// save the event
function publisher_log($publisher_id, $event_ar , $event_en)
{
//        $event               = new \App\Models\LogActivity;
//        $event->publisher_id = $publisher_id;
//        $event->event_ar     = $event_ar;
//        $event->event_en     = $event_en;
//        $event->save();
}

// get basic information from pagnation
function getBasicInfoPagantion($pagantion)
{
    $data = $pagantion->toArray();

    unset($data["data"]);

    foreach ($data as $key => $value)
    {
        $data[$key] = is_null($data[$key])?"":$data[$key];
    }
    return $data;
}

//get collection from pagantion
function getCollectionPagantion($pagantion)
{
    return $pagantion->getCollection();
}

//distance
function distance($lat,$lon,$_user, $unit) {
    $user = $_user;
    if ($user == null){
        return 0;
    }
    elseif (($user['lat'] == $lat) && ($user['lng'] == $lon)) {
        return 0;
    }
    else {
        $theta = $user['lng'] - $lon;
        $dist = sin(deg2rad($user['lat'])) * sin(deg2rad($lat)) +  cos(deg2rad($user['lat'])) * cos(deg2rad($lat)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return (int)($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }
}

// send fcmm
function send_FCM ($user, $data)
{
    $lang = $user->lang == 'ar' ? 'ar' : 'en';
    $datum = [
        'title' => $lang == 'ar' ? $data['title_ar'] : $data['title_en'],
        'msg'   => $lang == 'ar' ? $data['msg_ar'] : $data['msg_en'],
        'type' =>  $data['type']
    ];
    if(isset($data['pid'] ))
        $datum['pid']=$data['pid'];
    $optionBuilder = new \LaravelFCM\Message\OptionsBuilder();
    $optionBuilder->setTimeToLive(60 * 20);
    $notificationBuilder = new \LaravelFCM\Message\PayloadNotificationBuilder($datum['title']);
    $notificationBuilder->setBody($datum['msg'])
        ->setSound('default');
    $dataBuilder = new \LaravelFCM\Message\PayloadDataBuilder();
    $tokens = $user->device_id;
    if($tokens == null){
        return 0;
    }
    $dataBuilder->addData($datum);
    $option = $optionBuilder->build();
    $datum = $dataBuilder->build();
    $notification = $user->device_type == 'ios' ? $notificationBuilder->build() : null;
    if($user->device_type == 'ios'){
        $downstreamResponse = \FCM::sendTo($tokens, $option, $notification, $datum);
    }else{
        $downstreamResponse = \FCM::sendTo($tokens, $option, null, $datum);
    }
    $downstreamResponse->numberSuccess();
    $downstreamResponse->numberSuccess();
    $downstreamResponse->numberSuccess();
    $downstreamResponse->numberFailure();
    $downstreamResponse->numberModification();

//    dd($downstreamResponse);
}

// get near
 function get_near_stores($lat , $lng, $disance=5, $unit='k'){

     $type        = 6371;

     if($unit=='m')
         $type  = 3959;
     $query = "SELECT id 
                    , ( $type * acos ( cos ( radians(". $lat .") ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(".  $lng .") ) + sin ( radians(". $lat .") ) * sin( radians( lat ) ) ) )
                     AS `distance` FROM `store_places` where status='1' HAVING distance <= $disance  ";
     $ids          =  collect(\DB::select($query))->pluck('id')->toArray();
     return $ids;
 }

 // get near
function get_near($lat , $lng, $disance=5, $unit='k', $table="store_places", $addation=""){

    $type        = 6371;

    if($unit=='m')
        $type  = 3959;
    $query = "SELECT id 
                    , ( $type * acos ( cos ( radians(". $lat .") ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(".  $lng .") ) + sin ( radians(". $lat .") ) * sin( radians( lat ) ) ) )
                     AS `distance` FROM `{$table}` {$addation} HAVING distance <= $disance  ";
    $ids          =  collect(\DB::select($query))->pluck('id')->toArray();
    return $ids;
}
?>

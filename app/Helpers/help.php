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
        $event               = new \App\Models\LogActivity;
        $event->publisher_id = $publisher_id;
        $event->event_ar     = $event_ar;
        $event->event_en     = $event_en;
        $event->save();
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

?>
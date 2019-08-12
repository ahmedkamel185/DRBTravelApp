<?php
use File;
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
    File::delete('uploads/trips/'.$trip->map_screen_shot);
    foreach ($trip->resources as $resource){
        File::delete('uploads/tripResources/'.$resource->resource);
    }
}

?>
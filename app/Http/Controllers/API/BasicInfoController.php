<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StoreType;

class BasicInfoController extends Controller
{
    /************************
     *    help
     * **********************/

    //response storeType
    protected  function  responseStoreType($storeType)
    {
        $res['id'] = $storeType['id'];
        $res['name_ar'] = $storeType['name_ar'];
        $res['name_en'] = $storeType['name_en'];
        $res['icon']    = asset('uploads/storeTypes').'/'.$storeType->icon;
        return $res;

    }
    /*============================*/
    //get  trips type avaialabe
    public function getStoresTypes(Request $request){

        $storeTypes     = StoreType::all();
        $data           = $storeTypes->map(function ($type){
            return $this->responseStoreType($type);
        });
        return response()->json(
            [
                'key' => 'success',
                'status' => true,
                'data' => ['store-type'  =>  $data],
                'msg'=>""
            ]
        );

    }
}

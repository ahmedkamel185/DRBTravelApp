<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StoreType;
use App\Models\Setting;
use Validator;

class BasicInfoController extends Controller
{
    /************************
     *    help
     * **********************/

    //response storeType
    protected  function  responseStoreType($storeType)
    {
        $res['id']      = $storeType['id'];
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
                
                'status' => true,
                'data' => ['store-type'  =>  $data],
                'msg'=>""
            ]
        );

    }

    public function getTerms(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'lang'      =>"required|in:ar,en"
        ]);

        if ($validator->passes()) {
            $lang    = $request['lang'];
            if ($lang == 'ar')
                $terms    = Setting::pluck('terms_ar');
            else
                $terms    = Setting::pluck('terms_en');


            return response()->json(
                    [

                        'status' => true,
                        'data' => ['terms'=>$terms],
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

    public function getAbout(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'lang'      =>"required|in:ar,en"
        ]);

        if ($validator->passes()) {
            $lang    = $request['lang'];
            if ($lang == 'ar')
                $terms    = Setting::pluck('about_ar');
            else
                $terms    = Setting::pluck('about_en');


            return response()->json(
                [

                    'status' => true,
                    'data' => ['terms'=>$terms],
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

    public function getContact(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'lang'      =>"required|in:ar,en"
        ]);

        if ($validator->passes()) {
            $lang    = $request['lang'];
            if ($lang == 'ar')
                $terms    = Setting::pluck('contact_us_ar');
            else
                $terms    = Setting::pluck('contact_us_en');


            return response()->json(
                [

                    'status' => true,
                    'data' => ['terms'=>$terms],
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


}

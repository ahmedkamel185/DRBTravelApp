<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\StoreType;
use App\Models\RiskType;
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

    protected  function  responseRiskType($riskType)
    {
        $res['id']      = $riskType['id'];
        $res['name_ar'] = $riskType['name_ar'];
        $res['name_en'] = $riskType['name_en'];
        $res['icon']    = asset('uploads/riskTypes').'/'.$riskType->icon;
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

// get risk type
    public function getRiskTypes(Request $request){

        $riskTypes     = RiskType::all();
        $data           = $riskTypes->map(function ($type){
            return $this->responseRiskType($type);
        });
        return response()->json(
            [

                'status' => true,
                'data' => ['risk-type'  =>  $data],
                'msg'=>""
            ]
        );

    }
    public function getSettings(){
        $settings = Setting::all();
                return response()->json(
            [
                'status' => true,
                'data' => ['settings'=>$settings],
                'msg'=>""
            ]
        );

    }

//
//
//    public function getTerms(Request $request)
//    {
//        $validator=Validator::make($request->all(),[
//            'lang'      =>"required|in:ar,en"
//        ]);
//
//        if ($validator->passes()) {
//            $lang    = $request['lang'];
//            if ($lang == 'ar')
//                $terms    = Setting::pluck('terms_ar');
//            else
//                $terms    = Setting::pluck('terms_en');
//
//
//            return response()->json(
//                    [
//
//                        'status' => true,
//                        'data' => ['terms'=>$terms],
//                        'msg'=>""
//                    ]
//                );
//        }else{
//            foreach ((array)$validator->errors() as $key => $value){
//                foreach ($value as $msg){
//                    return response()->json(['status' => false, 'msg' => $msg[0]]);
//                }
//            }
//        }
//    }
//
//    public function getAbout(Request $request)
//    {
//        $validator=Validator::make($request->all(),[
//            'lang'      =>"required|in:ar,en"
//        ]);
//
//        if ($validator->passes()) {
//            $lang    = $request['lang'];
//            if ($lang == 'ar')
//                $terms    = Setting::pluck('about_ar');
//            else
//                $terms    = Setting::pluck('about_en');
//
//
//            return response()->json(
//                [
//
//                    'status' => true,
//                    'data' => ['terms'=>$terms],
//                    'msg'=>""
//                ]
//            );
//        }else{
//            foreach ((array)$validator->errors() as $key => $value){
//                foreach ($value as $msg){
//                    return response()->json(['status' => false, 'msg' => $msg[0]]);
//                }
//            }
//        }
//    }
//
//    public function getMobile()
//    {
//        $mobile    = Setting::pluck('mobile');
//            return response()->json(
//                [
//                    'status' => true,
//                    'data' => ['mobile'=>$mobile],
//                    'msg'=>""
//                ]
//            );
//    }
//
//    public function getWhats()
//    {
//        $whats    = Setting::pluck('whats');
//        return response()->json(
//            [
//                'status' => true,
//                'data' => ['whats'=>$whats],
//                'msg'=>""
//            ]
//        );
//    }
//
//    public function getYoutube()
//    {
//        $youtube    = Setting::pluck('youtube');
//        return response()->json(
//            [
//                'status' => true,
//                'data' => ['youtube'=>$youtube],
//                'msg'=>""
//            ]
//        );
//    }
//
//    public function getFacebook()
//    {
//        $face    = Setting::pluck('facebook');
//        return response()->json(
//            [
//                'status' => true,
//                'data' => ['facebook'=>$face],
//                'msg'=>""
//            ]
//        );
//    }
//
//    public function getTwitter()
//    {
//        $twitter    = Setting::pluck('twitter');
//        return response()->json(
//            [
//                'status' => true,
//                'data' => ['twitter'=>$twitter],
//                'msg'=>""
//            ]
//        );
//    }
//
//    public function getLinked()
//    {
//        $linked    = Setting::pluck('linked');
//        return response()->json(
//            [
//                'status' => true,
//                'data' => ['linked'=>$linked],
//                'msg'=>""
//            ]
//        );
//    }
}

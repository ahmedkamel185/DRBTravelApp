<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use URL;
use Image;
use Validator;
use App\Models\Risk as Risk;


class RisksController extends Controller
{
    //
    protected function responseRisk($risk)
    {
        $res['lat']             =  $risk->lat;
        $res['lng']             =  $risk->lng;
        $res['address']         =  $risk->address;
        $image                  =  is_null($risk['image'])? "default_image.png" : $risk['image'];
        $res['image']           =  asset('uploads/risks') . '/' . $image;
        $risk['desc']           =  $risk->desc;
        $res['status']          =  $risk->status;
        $res['type']            =  $risk->type;

        return $res;
    }



    public function addRisk(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                "lat"                 => 'required',
                'lng'                 => 'required',
                'address'             => 'required|min:2|max:190',
                'image'               => 'nullable|image',
                'desc'                => 'required',
                'publisher_id'        => 'required|exists:publishers,id',
            ]);

        if ($validator->passes()){
            $risk                       = new Risk;
            $risk->lat                  = $request['lat'];
            $risk->lng                  = $request['lng'];
            $risk->address              = $request['address'];
            $risk->desc                 = $request['desc'];
            $risk->publisher_id         = $request['publisher_id'];
            if ($request->has('type'))
            {

                $risk->type                 = $request['type'];
            }

            if($request['image'])
            {
                $photo=$request->image;
                $name = date('d-m-y').time().rand().'.'.$photo->getClientOriginalExtension();
                Image::make($photo)->save('uploads/risks/'.$name);
                $risk->image = $name;

            }
            $risk->save();
            $msg = $request['lang'] == 'ar' ? ' تم اضافه خطر جديد.' : ' danger is added successfully.';
            return response()->json(
                [
                   
                    'status' => true,
                    'data' => ['risk'=>$risk->id],
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
    //============================================//
    //show all risks

    public function showRisks(Request $request)
    {
        $risks = Risk::where('status', true)->get();
        $data = $risks->map(function ($type){
            return $this->responseRisk($type);
        });

        return response()->json(
            [
               
                'status' => true,
                'data' => ['risks'  =>  $data],
                'msg'=>""
            ]
        );
    }

    //===============================================//
    // show risk by type
    public function showTypeRisks(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                "risk_type"          => 'required',

            ]);
        if ($validator->passes())
        {

            $risks = Risk::where('status', true)->where('type',$request->risk_type)->get();
            $data = $risks->map(function ($type){
                return $this->responseRisk($type);
            });

            return response()->json(
                [
                   
                    'status' => true,
                    'data' => ['risks'  =>  $data],
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




    //==============================================//
    // edit risk
    public function editRisks(Request $request)
    {
        $validator = Validator::make($request->all(),[
            "lat"                 => 'required',
            'lng'                 => 'required',
            'address'             => 'required|min:2|max:190',
            'image'               => 'nullable|image',
            'desc'                => 'required',
            'publisher_id'        => 'required|exists:publishers,id',
            'risk_id'             => 'required|exists:risks,id'
        ]);

        if ($validator->passes()){
            $risk                = Risk::find($request->risk_id);
            if ($request->publisher_id != $risk->publisher_id)
            {
                $msg = $request['lang'] == 'ar' ? 'لست صاحب  الخطر .' : ' you are not owner of danger';
                return response()->json(
                    [
                       
                        'status' => true,
                        'data' => ['risk'=>$risk->id],
                        'msg'=>$msg
                    ]
                );
            }



            $risk->lat                  = $request['lat'];
            $risk->lng                  = $request['lng'];
            $risk->address              = $request['address'];
            $risk->desc                 = $request['desc'];
            $risk->publisher_id         = $request['publisher_id'];

            if($request['image'])
            {
                $photo=$request->image;
                if($risk->image != 'default_image.png') {
                    File::delete('uploads/risks/'.$risk->image);
                }

                $name = date('d-m-y').time().rand().'.'.$photo->getClientOriginalExtension();
                Image::make($photo)->save('uploads/risks/'.$name);
                $risk->image = $name;

            }
            $risk->save();
            $msg = $request['lang'] == 'ar' ? ' تم تعديل الخطر .' : ' danger is updated successfully.';
            return response()->json(
                [
                   
                    'status' => true,
                    'data' => ['risk'=>$risk->id],
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

    //=============================================/
    // delete risk
    public function deleteRisks(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'publisher_id'        => 'required|exists:publishers,id',
            'risk_id'             => 'required|exists:risks,id'
        ]);

        if ($validator->passes()){
            $risk                = Risk::find($request->risk_id);
            if ($request->publisher_id != $risk->publisher_id)
            {
                $msg = $request['lang'] == 'ar' ? 'لست صاحب  الخطر .' : ' you are not owner of danger';
                return response()->json(
                    [
                       
                        'status' => true,
                        'data' => ['risk'=>$risk->id],
                        'msg'=>$msg
                    ]
                );
            }

                if($risk->image != 'default_image.png') {
                    File::delete('uploads/risks/'.$risk->image);
                }

            $risk->delete();
            $msg = $request['lang'] == 'ar' ? ' تم مسح الخطر .' : ' danger is deleted successfully.';
            return response()->json(
                [
                   
                    'status' => true,
                    'data' => ['risk'=>$risk->id],
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










}

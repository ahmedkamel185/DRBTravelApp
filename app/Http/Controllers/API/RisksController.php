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
    // vote risk
    protected function riskCommentResponse($risk)
    {
        $res['vote']                 = $risk->vote;
        $res['publisher_id']         = $risk->publisher_id;
        $res['risk_id']              = $risk->risk_id;
        return $res;
    }



    //response user
    protected function responseUser($user, $type=1)
    {
        $res["id"]              = $user->id;
        $res["username"]        = $user->username;
        $res["display_name"]    = $user->display_name;
        $image                  = is_null($user['image'])? "default_image.png" : $user['image'];
        $res['image']           = asset('uploads/publishers') . '/' . $image;
        $res['type']            = 1;
        return $res;
    }




    //
    protected function responseRisk($risk, $publisher_id=null)
    {
        $res['id']              = $risk->id;
        $res['lat']             =  $risk->lat;
        $res['lng']             =  $risk->lng;
        $res['address']         =  $risk->address;
        $res['desc']            = $risk->desc;
        $res['created_at']            = strtotime($risk->created_at) * 1000;
        $image                  =  is_null($risk['image'])? "default_image.png" : $risk['image'];
        $res['image']           =  asset('uploads/risks') . '/' . $image;
        $res['status']          =  $risk->status;
        $res['riskType']        = $this->responseRiskType($risk->riskType);
        $res['publisher']       = $this->responseUser($risk->publisher);
        $res['yes']             = $risk->risksComment->where('vote','yes')->count();
        $res['no']              = $risk->risksComment->where('vote','no')->count();
        $res['act']             = $risk->risksComment->where('publisher_id',$publisher_id)->pluck('vote')->first();
       if (($res['act']))
       {
           $res['vote'] = true;
       }else{
           $res['vote'] = false;

       }

        return $res;
    }

    // response risk
    protected  function  responseRiskType($riskType)
    {
        $res['name_ar'] = $riskType['name_ar'];
        $res['name_en'] = $riskType['name_en'];
        $res['icon']    = asset('uploads/riskTypes').'/'.$riskType->icon;
        return $res;

    }

    // repsonse near
    protected function responsNear($place,$lat,$lng ,$unit){
        $res['risk'] =  $this->responseRisk($place);
//        $res['publisher']  =  $this->responseUser($place->publisher);
        $res['disance']=  distance($lat , $lng ,$place,$unit);
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
                'risk_type_id'        => 'required|exists:risk_types,id',

            ]);

        if ($validator->passes()){
            $risk                       = new Risk;
            $risk->lat                  = $request['lat'];
            $risk->lng                  = $request['lng'];
            $risk->address              = $request['address'];
            $risk->desc                 = $request['desc'];
            $risk->publisher_id         = $request['publisher_id'];
            $risk->risk_type_id         = $request['risk_type_id'] ;

            if($request['image'])
            {
                $photo=$request->image;
                $name = date('d-m-y').time().rand().'.'.$photo->getClientOriginalExtension();
//                Image::make($photo)->save('uploads/risks/'.$name);
                $photo->move(public_path('uploads/risks'), $name);
                $risk->image = $name;

            }
            $risk->save();
            $msg = $request['lang'] == 'ar' ? ' تم اضافه خطر جديد.' : ' danger is added successfully.';
            return response()->json(
                [

                    'status' => true,
                    'data' => ['risk'=>$this->responseRisk($risk)],
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


    public function addRisk_without_image(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                "lat"                 => 'required',
                'lng'                 => 'required',
                'address'             => 'required|min:2|max:190',
                'desc'                => 'required',
                'publisher_id'        => 'required|exists:publishers,id',
                'risk_type_id'        => 'required|exists:risk_types,id',
                

            ]);

        if ($validator->passes()){
            $risk                       = new Risk;
            $risk->lat                  = $request['lat'];
            $risk->lng                  = $request['lng'];
            $risk->address              = $request['address'];
            $risk->publisher_id         = $request['publisher_id'];
            $risk->risk_type_id         = $request['risk_type_id'] ;
            $risk->image = 'null';

            if ($request['desc']) {
                $risk->desc                 = $request['desc'];
            }
            else
            {
                $risk->desc                 = 'null';
            }



            $risk->save();
            $msg = $request['lang'] == 'ar' ? ' تم اضافه خطر جديد.' : ' danger is added successfully.';
            return response()->json(
                [

                    'status' => true,
                    'data' => ['risk'=>$this->responseRisk($risk)],
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
        $validator=Validator::make($request->all(),[
            'publisher_id'   => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {

            $risks = Risk::where('status', true)->orderBy('created_at','desc')->simplePaginate(10);
            $meta       = getBasicInfoPagantion($risks);
            $data = getCollectionPagantion($risks)->map(function ($type)use($request){
                return $this->responseRisk($type ,$request['publisher_id']);
            });
            $res['risks']       = $data;
            $res['meta']        = $meta;
            return response()->json(
                [

                    'status' => true,
                    'data' => ['risks-list'  =>  $res],
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


    // get risk by id

    public function getRisk(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'publisher_id'   => 'required|exists:publishers,id',
            'id'             => 'required|exists:risks,id',
        ]);

        if ($validator->passes()) {

            $risk       = Risk::where('status', true)->where('id', $request['id'])->first();
            if(!$risk) {
                $msg    = $request['lang'] == "ar" ? "ليست متاحه حاليا" : "not available now";
                return response()->json(['status' => false, 'msg' => $msg]);
            }

            return response()->json(
                [

                    'status' => true,
                    'data' => ['risk'  =>  $this->responseRisk($risk ,$request['publisher_id']) ],
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
//                Image::make($photo)->save('uploads/risks/'.$name);
                $photo->move(public_path('uploads/risks'), $name);
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


    //=========================================
    public  function nearRisks(Request $request){
        $validator = Validator::make($request->all(),
            [
                'lat'                 => 'required',
                'lng'                 => 'required',
                'distance'            => 'nullable',
                'type'                => 'nullable|in:k,m'
            ]);

        if ($validator->passes()) {
            $lat        = $request['lat'];
            $lng        = $request['lng'];
            $disance    = is_null($request['distance'])?5:(int)$request['distance'];
            $type        = 6371;
            if($request['type']=='m')
                $type  = 3959;
            $query = "SELECT id 
                    , ( $type * acos ( cos ( radians(". $lat .") ) * cos( radians( lat ) ) * cos( radians( lng ) - radians(".  $lng .") ) + sin ( radians(". $lat .") ) * sin( radians( lat ) ) ) )
                     AS `distance` FROM `risks`  HAVING distance <= $disance ";
            $ids          =  collect(\DB::select($query))->pluck('id')->toArray();

            $storePlaces  =  Risk::whereIn('id', $ids)->get();

            $unit         = $request['type'];
            $data         = $storePlaces->map(function ($place) use ($lat, $lng ,$unit){
                return $this->responsNear($place,$lat, $lng, $unit);
            });
            return response()->json(
                [
                    'status' => true,
                    'data'  => ["near"=>$data],
                    'msg'   => ""
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

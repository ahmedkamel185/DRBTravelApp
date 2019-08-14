<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use File;
use URL;
use Image;
use Validator;
use App\Models\RiskComment as RiskComment;


class RiskCommentController extends Controller
{
    protected function riskCommentResponse($risk)
    {
        $res['vote']                 = $risk->vote;
        $res['publisher_id']         = $risk->publisher_id;
        $res['risk_id']              = $risk->risk_id;
        return $res;
    }

    public function addRiskComment(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'publisher_id'                 => 'required|exists:publishers,id',
            'risk_id'                      => 'required|exists:risks,id',
            'vote'                         => 'required|in:yes,no',
            'risk_comment_id'              => 'required|exists:risk_comments,id',

        ]);
        if ($validator->passes()){

        $publisher_id = !!RiskComment::where('publisher_id', $request->publisher_id)->first();
        $risk_id = !!RiskComment::where('risk_id', $request->risk_id)->first();
        if ($publisher_id && $risk_id)
        {
                $riskComment                       = RiskComment::find($request->risk_comment_id);
                $riskComment->vote                 = $request['vote'];
                $riskComment->risk_id              = $request['risk_id'];
                $riskComment->publisher_id         = $request['publisher_id'];

                $riskComment->save();
                $msg = $request['lang'] == 'ar' ? 'تم تعديل تعليق علي الخطر ':" comment updated success";
                return response()->json(['status'=>true,'data' => "", 'msg' => $msg]);

            }


        $riskComment                       = new RiskComment;
        $riskComment->vote                 = $request['vote'];
        $riskComment->risk_id              = $request['risk_id'];
        $riskComment->publisher_id         = $request['publisher_id'];

        $riskComment->save();


            $msg = $request['lang'] == 'ar' ? 'تم اضافه تعليق علي الخطر ':" comment add success";
            return response()->json(['status'=>true,'data' => "", 'msg' => $msg]);

        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }

    }


    //=================================================//
    // remove comment
    public function removeComment(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'publisher_id'                 => 'required|exists:publishers,id',
            'risk_comment_id'              => 'required|exists:risk_comments,id',
        ]);
        if ($validator->passes())
        {
          $risk_comment = RiskComment::find($request->risk_comment_id);
            if ($request->publisher_id !=$risk_comment->publisher_id)
            {
                $msg = $request['lang'] == 'ar' ? 'لست صاحب  الكومنت .' : ' you are not owner of comment';
                return response()->json(
                    [
                        'key' => 'success',
                        'status' => true,
                        'data' =>'',
                        'msg'=>$msg
                    ]
                );
            }

            $risk_comment->delete();
            $msg = $request['lang'] == 'ar' ? ' تم مسح الكومنت .' : ' comment is deleted successfully.';
            return response()->json(
                [
                    'key' => 'success',
                    'status' => true,
                    'data' => ['risk'=>$risk_comment->id],
                    'msg'=>$msg
                ]
            );

        } else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }

    }


























}

<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Suggest;
use Image;
use Validator;
use File;
use App\Models\Block;
use App\Models\CommentSuggest as Comment;
use App\Models\LikeSuggest as Like;

use App\Notifications\Comment as commentNotify;
use App\Notifications\Like as LikeNotify;

class SuggestController extends Controller
{
    //response suggest
    protected function responseSuggest($suggest, $user_id = 0)
    {
        $res["id"]              = $suggest->id;
        $res["lat"]             = $suggest->lat;
        $res["lng"]             = $suggest->lng;
        $res["address"]         = $suggest->address;
        $res["desc"]            = $suggest->desc;
        $res["user_id"]         = $suggest->user_id;
        $image                  = is_null($suggest['image'])? "default_image.png" : $suggest['image'];
        $res['image']           = asset('uploads/suggests') . '/' . $image;
        $res['comments']        = $suggest->comments->count();
        $res['likes_count']     = $suggest->likes->count();
        $res['likes_latest']    = $res['likes_count'] >0? $suggest->likes()->latest()->first()->user->display_name:"";
        $res['created_at']      = $suggest->created_at->format('d-m-Y h:i a');
        return $res;
    }

    // response comment
    protected function   responseComment($comment){
        $res['id']             =  $comment->id;
        $res['body']           =  $comment->body;
        $res['user']           =  $this->responseUser($comment->user);
        $res['suggest_id']     = $comment->suggest_id;
        $res['created_at']     = $comment->created_at->format('d-m-Y h:i a');
        return $res;
    }
    // response like
    protected function responseLike($like){
        $res['id']             = $like['id'];
        $res['user']           = $this->responseUser($like->User);
        $res['suggest_id']     = $like->suggest_id;
        $res['created_at']     = $like->created_at->format('d-m-Y h:i a');
        return $res;
    }
    // response user
    protected function responseUser($user, $type=1)
    {
        $res["id"]              = $user->id;
        $res["username"]        = $user->username;
        $res["display_name"]    = $user->display_name;
        $image                  = is_null($user['image'])? "default_image.png" : $user['image'];
        $res['image']           = asset('uploads/publishers') . '/' . $image;
        $res['type']            = $type;
        return $res;
    }

    // add
    public  function addSuugest(Request $request){
        $validator=Validator::make($request->all(),[
            "lat"           => 'required',
            'lng'           => 'required',
            'address'       => 'required|min:2|max:190',
            'user_id'       => 'required|exists:publishers,id',
            'desc'          => 'required',
            'image'         => 'required|image'
        ]);
        if ($validator->passes()) {
            $suggest            = new Suggest;
            $suggest->lat       = $request['lat'];
            $suggest->lng       = $request['lng'];
            $suggest->address   = $request['address'];
            $suggest->user_id   = $request['user_id'];
            $suggest->desc      = $request['desc'];
            $photo=$request->image;
            $name = date('d-m-y').time().rand().'.'.$photo->getClientOriginalExtension();
            Image::make($photo)->save('uploads/tripResources/'.$name);
            $suggest->image      = $name;
            $suggest->save();
            publisher_log(
                $request['user_id'],
                ' لقد قمت باقتراخ مكان جديد   ',
                'you suggest new place'
            );
            $msg = $request['lang'] == 'ar' ? ' تم الاضافه بنجاح.' : ' sucessfull upload .';
            return response()->json(
                [
                    'status' => true,
                    'data' => ['suggest'=>""],
                    'msg'=>$msg
                ]
            );
        }
        else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // edit
    public  function editSuugest(Request $request){
        $validator=Validator::make($request->all(),[
            "lat"           => 'required',
            'lng'           => 'required',
            'address'       => 'required|min:2|max:190',
            'user_id'       => 'required|exists:publishers,id',
            'suggest_id'    => 'required|exists:suggests,id',
            'desc'          => 'required',
            'image'         => 'nullable|image'
        ]);
        if ($validator->passes()) {

            $suggest            = Suggest::find($request['suggest_id']);
            if($suggest->user_id != $request['user_id']){
                $msg = $request['lang'] == 'ar' ? ' المستخدم لايملك هذا الاقتراح .' : ' user not owner the suggest.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }
            $suggest->lat       = $request['lat'];
            $suggest->lng       = $request['lng'];
            $suggest->address   = $request['address'];
            $suggest->user_id   = $request['user_id'];
            $suggest->desc      = $request['desc'];
            if($request['image']){
                \File::delete('uploads/suggests/'.$suggest->name);
                $photo=$request->image;
                $name = date('d-m-y').time().rand().'.'.$photo->getClientOriginalExtension();
                Image::make($photo)->save('uploads/suggests/'.$name);
                $suggest->image      = $name;
            }
            $suggest->save();
            $msg = $request['lang'] == 'ar' ? ' تم التعديل بنجاح.' : ' sucessfull edit .';
            return response()->json(
                [
                    'status' => true,
                    'data' => ['suggest'=>""],
                    'msg'=>$msg
                ]
            );
        }
        else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // delte suugest
    public  function delteSuugest(Request $request){
        $validator=Validator::make($request->all(),[
            'user_id'       => 'required|exists:publishers,id',
            'suggest_id'    => 'required|exists:suggests,id',
        ]);
        if ($validator->passes()) {

            $suggest            = Suggest::find($request['suggest_id']);
            if($suggest->user_id != $request['user_id']){
                $msg = $request['lang'] == 'ar' ? ' المستخدم لايملك هذا الاقتراح .' : ' user not owner the suggest.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }
            \File::delete('uploads/suggests/'.$suggest->name);
            $suggest->delete();
            publisher_log(
                $request['user_id'],
                ' لقد قمت بحذف  اقتراح   ',
                'you delete suggest'
            );
            $msg = $request['lang'] == 'ar' ? ' تم الحذف بنجاح.' : ' sucessfull  delete .';
            return response()->json(
                [
                    'status' => true,
                    'data' => ['suggest'=>""],
                    'msg'=>$msg
                ]
            );
        }
        else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    //get suggest
    public  function getSuugest(Request $request){
        $validator=Validator::make($request->all(),[
            'user_id'       => 'required|exists:publishers,id',
            'suggest_id'    => 'required|exists:suggests,id',
        ]);
        if ($validator->passes()) {

            $suggest            = Suggest::find($request['suggest_id']);

            return response()->json(
                [
                    'status' => true,
                    'data' => ['suggest'=>$this->responseSuggest($suggest, $request['user_id'])],
                    'msg'=>""
                ]
            );
        }
        else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    //get suggest
    public  function getSuugests(Request $request){
        $validator=Validator::make($request->all(),[
            'user_id'       => 'required|exists:publishers,id',
        ]);
        if ($validator->passes()) {
            $blocks             = Block::buldBlockId($request['user_id']);
            $blockingMe         = Block::buldBlockerId($request['user_id']);
            $allBlocks          = array_merge($blocks, $blockingMe);
            $suggests           = Suggest::whereNotIn('user_id', $allBlocks )->latest()->simplePaginate(10);
            $meta               = getBasicInfoPagantion($suggests);
            $data               = getCollectionPagantion($suggests)->map(function ($suggest) use($request){
                return $this->responseSuggest($suggest, $request['user_id']);
            });
            return response()->json(
                [
                    'status' => true,
                    'data' => ['suggest'=>$data],
                    'msg'  =>"" ,
                    'meta' => $meta
                ]
            );
        }
        else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    //get suggest
    public  function getUserSuggest(Request $request){
        $validator=Validator::make($request->all(),[
            'user_id'       => 'required|exists:publishers,id',
            'publisher_id'  => 'required|exists:publishers,id',
        ]);
        if ($validator->passes()) {
            $blocks             = Block::buldBlockId($request['user_id']);
            $blockingMe         = Block::buldBlockerId($request['user_id']);
            $allBlocks          = array_merge($blocks, $blockingMe);
            if(in_array($request['publisher_id'], $allBlocks))
            {
                $msg = $request['lang'] == 'ar' ? ' لايمكنك التواصل مع هذا المستخدم.' : ' you cna\'t trait with this user.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }
            $suggests           = Suggest::where('user_id', $request['publisher_id'] )->simplePaginate(10);
            $meta               = getBasicInfoPagantion($suggests);
            $data               = getCollectionPagantion($suggests)->map(function ($suggest) use($request){
                $this->responseSuggest($suggest, $request['user_id']);
            });
            return response()->json(
                [
                    'status' => true,
                    'data' => ['suggest'=>$data],
                    'msg'  =>"" ,
                    'meta' => $meta
                ]
            );
        }
        else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }


    // save comment
    public function saveComment(Request $request){
        $validator=Validator::make($request->all(),[
            'suggest_id'     => 'required|exists:suggests,id',
            'user_id'        => 'required|exists:publishers,id',
            'body'           => 'required',
        ]);

        if ($validator->passes()) {
                $comment                = new Comment;
                $comment->suggest_id    = $request['suggest_id'] ;
                $comment->user_id       = $request['user_id'] ;
                $comment->body          = $request['body'] ;
                $comment->save();

                publisher_log(
                    $request['user_id'],
                    ' لقد قمت باضافة تعليق ل '.$comment->suggest->publisher->display_name,
                    'you  add  the comment to'.$comment->suggest->publisher->display_name
                );
               $comment->suggest->publisher->notify(new commentNotify
                (
                    $comment->suggest->publisher,
                    "suggest",
                    $comment->id,
                    $comment
                )
               );
                $msg = $request['lang'] == 'ar' ? ' تم اضافة التعليق.' : ' sucessfull add comment.';
                return response()->json(
                    [
                        'status' => true,
                        'data'   => [ "comment" => $this->responseComment($comment) ],
                        'msg'    =>$msg
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

    // delete comment
    public function deleteComment(Request $request){

        $validator=Validator::make($request->all(),[
            'comment_id'     => 'required|exists:comment_suggests,id',
            'user_id'        => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {
            $comment                = Comment::find($request['comment_id']);
            if( $comment->user_id == $request['user_id'] ){
                $comment->delete();
                $msg = $request['lang'] == 'ar' ? ' تم حذف التعليق.' : ' sucessfull delete comment.';
                return response()->json(
                    [
                        'status' => true,
                        'data'   => "",
                        'msg'    =>$msg
                    ]
                );
            }else{
                $msg = $request['lang'] == 'ar' ? ' التعليق غير موجود ف تعليقاتك .' : 'comment not found in comments.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }

        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // get comment
    public function getComment(Request $request){

        $validator=Validator::make($request->all(),[
            'comment_id'     => 'required|exists:comment_suggests,id',
            'user_id'        => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {
            $comment                = Comment::find($request['comment_id']);
            if( $comment->user_id == $request['user_id'] ){
                return response()->json(
                    [
                        'status' => true,
                        'data'   =>[ "comment" => $this->responseComment($comment) ],
                        'msg'    =>""
                    ]
                );
            }else{
                $msg = $request['lang'] == 'ar' ? ' التعليق غير موجود ف تعليقاتك .' : 'comment not found in comments.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }

        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // update comment
    public function updateComment(Request $request){

        $validator=Validator::make($request->all(),[
            'comment_id'     => 'required|exists:comment_suggests,id',
            'user_id'        => 'required|exists:publishers,id',
            'body'           => 'required'
        ]);

        if ($validator->passes()) {
            $comment                = Comment::find($request['comment_id']);
            if( $comment->user_id == $request['user_id'] ){
                $comment->body    = $request['body'];
                return response()->json(
                    [
                        'status' => true,
                        'data'   => [ "comment" => $this->responseComment($comment) ],
                        'msg'    =>""
                    ]
                );
            }else{
                $msg = $request['lang'] == 'ar' ? ' التعليق غير موجود ف تعليقاتك .' : 'comment not found in comments.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }

        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // get all comment
    public function getComments(Request $request){

        $validator=Validator::make($request->all(),[
            'suggest_id'     => 'required|exists:suggests,id',
            'user_id'        => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {
            $suggest                = Suggest::find($request['suggest_id']);

            // check privacy
            if($suggest->privacy != "private"){
                $data  = $suggest->comments->map(function ($comment){
                    return $this->responseComment($comment);
                }) ;
                return response()->json(
                    [
                        'status' => true,
                        'data'   =>['comments'=> $data],
                        'msg'    =>""
                    ]
                );
            }else{
                $msg = $request['lang'] == 'ar' ? ' ليس لديك صلاحيه للتعليق .' : ' privacy not allow you to comment.';
                return response()->json(
                    [
                        'status' => false,
                        'data' => "",
                        'msg'=>$msg
                    ]
                );
            }


        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }


    // likes action delete and add
    public function likeAction(Request $request){
        $validator=Validator::make($request->all(),[
            'suggest_id'     => 'required|exists:suggests,id',
            'user_id'        => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {
                $like     = Like::where('suggest_id', $request['suggest_id'])
                    ->where('user_id', $request['user_id'])->first();
                if($like) {
                    $msg = $request['lang'] == 'ar' ? ' تم حذف الاعجاب.' : ' sucessfull delete liked.';
                    $suugest  = Suggest::find($request['suggest_id']);
                    $suugest->publisher->notify(new LikeNotify
                        (
                            $suugest->publisher,
                            "suggest",
                            $suugest->id,
                            $like
                        )
                    );
                    publisher_log(
                        $request['user_id'],
                        ' لقد قمت بحذف الاعجاب ل '.$like->suggest->publisher->display_name,
                        'you delete the like for'.$like->suggest->publisher->display_name
                    );
                    $like->delete();
                } else{
                    $like                   = new Like;
                    $like->user_id          = $request['user_id'];
                    $like->suggest_id       = $request['suggest_id'];
                    $like->save();
                    $msg = $request['lang'] == 'ar' ? ' تم الاعجاب.' : ' sucessfull liked.';
                    publisher_log(
                        $request['user_id'],
                        ' لقد قمت  بالاعجاب ل '.$like->suggest->publisher->display_name,
                        'you do the like for'.$like->suggest->publisher->display_name
                    );
                }

                return response()->json(
                    [
                        'status' => true,
                        'data'   => "",
                        'msg'    =>$msg
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

    // list like
    public function getLikes(Request $request){

        $validator=Validator::make($request->all(),[
            'suggest_id'     => 'required|exists:suggests,id',
            'user_id'        => 'required|exists:publishers,id',
        ]);

        if ($validator->passes()) {
            $suggest                = Suggest::find($request['suggest_id']);

            // check privacy
                $data  = $suggest->likes->map(function ($like){
                    return $this->responseLike($like);
                }) ;
                return response()->json(
                    [
                        'status' => true,
                        'data'   =>['likes'=> $data],
                        'msg'    =>""
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

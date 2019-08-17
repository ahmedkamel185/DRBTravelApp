<?php

namespace App\Http\Controllers\API;

use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use URL;
use Image;
use Validator;
use App\Models\Publisher as User;
use App\Models\Block ;
use App\Models\Follower;
use File;
use Illuminate\Support\Str;
use App\Notifications\Follow as FollowNotify;

class PublisherController extends Controller
{
    /**********************
     *     helper
     * ********************
     * */

    /*
     * @optional take add flage to return valdation for add or edit
     * @return arry of valdation
     * */
    //activity response
    protected function reponseActivity($activity)
    {
        $publiser                = $activity->publisher;
        $res['publisher']        = $this->responsePublisherUser($publiser);
        $res['event_ar']         = $activity->event_ar;
        $res['event_en']         = $activity->event_en;
        $res['created_at']       = $activity->created_at->format('d-m-Y h:i a');
        return $res;
    }

    protected function responsePublisherUser($user){
        $res["id"]              = $user->id;
        $res["username"]        = $user->username;
        $res["display_name"]    = $user->display_name;
        $image                  = is_null($user['image'])? "default_image.png" : $user['image'];
        $res['image']           = asset('uploads/publishers') . '/' . $image;
        $res['type']            = 1;
        return $res;
    }
    //response user
    protected function responseUser($user, $type=1)
    {
        $res["id"]              = $user->id;
        $res["username"]        = $user->username;
        $res["display_name"]    = $user->display_name;
        $res["mobile"]          = $user->mobile;
        $res["email"]           = $user->email;
        $res["city"]            = $user->city;
        $res['bio']             = is_null($user['bio'])?"":$user['bio'];
        $image                  = is_null($user['image'])? "default_image.png" : $user['image'];
        $res['image']           = asset('uploads/publishers') . '/' . $image;
        $res['status']          = is_null($user['status'])?1:$user['status'];
        $res['verified']        = is_null($user['verified'])?1:$user['verified'];
        $res['follow']          = $user->follows()->count();
        $res['follower']        = $user->followers()->count();
        $res['type']            = 1;
        $res['notification_count']= $user->unreadNotifications()->count();
        return $res;
    }

    //response block
    protected  function responseBlock($block)
    {
        $res['id']          = $block['id'];
        $res['publisher']   = $this->responsePublisherUser($block->publisher);
        $res['created_at']  = $block->created_at->format('d-m-Y h:i a');
        return $res;

    }

    // reposne notifcation
    protected  function responseNotification($notify){
        $res['id']        = $notify['id'];
        $res['image']     = $notify->data['image'];
        $res['pid']       = $notify->data['pid'];
        $res['type']      = $notify->data['type'];
        $res['title_ar']  = $notify->data['title_ar'];
        $res['title_en']  = $notify->data['title_en'];
        $res['msg_ar']    = $notify->data['msg_ar'];
        $res['msg_en']    = $notify->data['msg_en'];
        $res['created_at']= $notify->created_at->format('d-m-Y h:i a');
        $res['readed_at'] = is_null($notify->read_at)?"":$notify->read_at->format('d-m-Y h:i a');
        return $res ;
    }
    /*===========================*/

    // sign in
    public function signIn(Request $request)
    {
        $validator=Validator::make($request->all(),[
            "username"         => 'required|min:2|max:190|unique:publishers',
            'display_name'     => 'required|min:2|max:190',
            'image'            => 'nullable|image',
            'mobile'           => 'required|min:2|max:190|unique:publishers,mobile',
            'email'            => 'required|email|min:2|max:190|unique:publishers,email',
            'city'             => 'required|min:2|max:190',
            'password'         => 'required|min:6|max:190',
            'bio'              => 'nullable',
            'device_id'        => 'required',
            'device_type'      => 'required|in:ios,android'
        ]);
        if ($validator->passes()) {
            $user               = new User;
            $user->username     = $request['username'];
            $user->display_name = $request['display_name'];
            $user->mobile       = $request['mobile'];
            $user->bio          = $request['bio'];
            $user->email        = convert2english($request['email']);
            $user->password     = bcrypt(convert2english($request['password']));
            $user->city         = $request['city'];
            $user->device_id    = $request['device_id'];
            $user->device_type  = $request['device_type'];
            $user->save();
            return response()->json(
                    [
                        
                        'status' => true,
                        'data' => ['publisher'=>$this->responseUser($user)],
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

    // login
     public  function login(Request $request){

         $validator=Validator::make($request->all(),[
             'email'            => 'required|email|min:2|max:190',
             'password'         => 'required|min:6|max:190',
             'device_id'        => 'required',
             'device_type'      => 'required|in:ios,android'
         ]);

         if ($validator->passes()) {
             if(! auth('publisher')->attempt( ['email'=> convert2english($request['email']) ,'password' => convert2english($request['password'] ) ])){
                 $msg = $request['lang'] == 'ar' ? ' كلمه المرور او الاميل غير صحيح.' : ' password or email is wrong.';
                 return response()->json(['status'=> false,'msg'=>$msg]);
             }
             $user = auth('publisher')->user();

             if($user->verified == 0){
                 $msg = $request['lang'] == 'ar' ? 'لم يتم تاكيد الحساب بعد.' : 'account not verfied .';
                 return response()->json(['status'=>false,'msg'=>$msg]);
             }

             if($user->status == 0){
                 $msg = $request['lang'] == 'ar' ? 'المستخدم محظور حاليا يمكن التواصل مع الاداره.' : ' user has been blocked can contact with adminstration.';
                 return response()->json(['status'=>false,'msg'=>$msg]);
             }

             return response()->json(
                 [
                     
                     'status' => true,
                     'data' => ['publisher' => $this->responseUser($user)],
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

     // get user profile
     public function getUser(Request $request){
         $validator=Validator::make($request->all(),[
             'user_id'          => 'required|exists:publishers,id',
         ]);
         if ($validator->passes()) {
             $user     = User::find($request['user_id']);
             return response()->json(
                 [
                     
                     'status' => true,
                     'data' => ['publisher'=>$this->responseUser($user)],
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

    // get user notifcation
    public function getNotifications(Request $request){
        $validator=Validator::make($request->all(),[
            'user_id'          => 'required|exists:publishers,id',
        ]);
        if ($validator->passes()) {
            $user           = User::find($request['user_id']);
            $notifications  = $user->notifications()->latest()->paginate(10);
            $user->unreadNotifications->markAsRead();
            $meta       = getBasicInfoPagantion($notifications);
            $data       = getCollectionPagantion($notifications)->map(function ($notify) use($request){
                return $this->responseNotification($notify);
            });
            return response()->json(
                [

                    'status' => true,
                    'data' => ['notifications'=> $data],
                    'msg'  =>"",
                    'meta' => $meta
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

    // delte notification
    public function deleteNotifications(Request $request)
    {

        $validator=Validator::make($request->all(),[
            'notification_id'   => 'required|exists:notifications,id',
        ]);
        if ($validator->passes()) {
            \DB::delete("delete from notifications where id= ?",[$request['Notification_id']]);
            $msg = $request["lang"] == "ar" ? " تم الحذف":"Sucessful delete";
            return response()->json(['key'=>'success','value'=>'1',"msg"=>$msg,]);
        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['key' => 'fail','value' => '0', 'msg' => $msg[0]]);
                }
            }
        }


    }

     // update profile
    public  function updateProfile(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'user_id'          => 'required|exists:publishers,id',
            "username"         => 'required|min:2|max:190',
            'display_name'     => 'required|min:2|max:190',
            'image'            => 'nullable|image',
            'mobile'           => 'required|min:2|max:190|unique:publishers,mobile,'.$request['user_id'],
            'email'            => 'required|email|min:2|max:190|unique:publishers,email,'.$request['user_id'],
            'city'             => 'required|min:2|max:190',
            'bio'             =>  'nullable',
        ]);

        if ($validator->passes()) {

            $user     = User::find($request['user_id']);
            $user->username     = $request['username'];
            $user->display_name = $request['display_name'];
            $user->mobile       = $request['mobile'];
            $user->bio          = $request['bio'];
            $user->email        = convert2english($request['email']);
            $user->city         = $request['city'];

            if($request['image'])
            {
                $photo=$request->image;
                if($user->image != 'default_image.png') {
                    File::delete('uploads/publishers/'.$user->image);
                }

                $name = date('d-m-y').time().rand().'.'.$photo->getClientOriginalExtension();
                Image::make($photo)->save('uploads/publishers/'.$name);
                $user->image = $name;

            }
            $user->save();
            $msg = $request['lang'] == 'ar' ? 'تم تعديل الحساب' : 'account update .';
            return response()->json(
                [
                    'key'   => 'success',
                    'status' => true,
                    'data'  => "",
                    'msg'   => $msg
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

    //change user password
    public  function changePassword(Request $request)
    {

        $validator = Validator::make($request->all(),[
            'user_id'                   =>'required|exists:publishers,id',
            'password'                  =>'required|min:6|max:190',
            'old_password'              =>'required|min:6|max:190',

        ]);

        if ($validator->passes())
        {
            $request["password"]    = convert2english($request["password"] );
            $request["old_password"]= convert2english($request["old_password"] );
            $user = User::find($request['user_id']);
            if(\Hash::check($request['old_password'],$user->password)) {
                $user['password'] = bcrypt($request['password']);
                $user->save();
                $arr['id'] = $user['id'];
                $msg = $request['lang'] == 'ar' ? 'تم تغير كلمة السر بنجاح ':" password change successfull";
                return response()->json([ 'status' => true, 'data' => "", 'msg' => $msg]);
            }else
            {
                $msg = $request['lang'] == 'ar' ? 'رقم السرى القديم غير صحيح ':"old password not correct";
                return response()->json(['status'=>false,'msg'=>$msg]);
            }
        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }

    }

    // chnage langue
    public function changeLang(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'user_id'   =>"required|exists:publishers,id",
            'lang'      =>"required|in:ar,en"
        ]);

        if ($validator->passes()) {
            $lang    = $request['lang'];
            $user    = User::find($request["user_id"]);
            $user["lang"]=$lang;
            $user->update();
            $msg = $lang=="ar"?"تم تغير اللغه بنجاح":"sucessfull change the langue";
            return response()->json(['status'=>true,'data' => "", 'msg' => $msg]);
        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // chnage langue
    public function logActivity(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'user_id'   =>"required|exists:publishers,id",
        ]);

        if ($validator->passes()) {
            $lang    = $request['lang'];
            $user    = User::find($request["user_id"]);
            $data    = $user->logs->map(function ($log){
               return $this->reponseActivity($log);
            });
            return response()->json(['status'=>true,'data' => ["activites"=>$data], 'msg' => ""]);
        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // delete all logs
    public function deleteLogActivity(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'user_id'   =>"required|exists:publishers,id",
        ]);

        if ($validator->passes()) {
            $lang    = $request['lang'];
            $user    = User::find($request["user_id"]);
            $user->logs()->delete();
            $msg     = $lang=="ar"?"تم الحذف بنجاح":"sucessfull delete the date";
            return response()->json(['status'=>true,'data' => ["activites"=>""], 'msg' => $msg]);
        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // block action
    public function blockAction(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'user_id'        =>"required|exists:publishers,id",
            'publisher_id'   =>"required|exists:publishers,id",
        ]);

        if ($validator->passes()) {
            $lang    = $request['lang'];
            if($request['user_id'] == $request['publisher_id']){
                $msg     = $lang=="ar"?"لايمكن حظر نفسك":"can\'t block your self";
                return response()->json(['status'=>false,'data' => ["blocks"=>""], 'msg' => $msg]);
            }
            $block   = Block::where('user_id', $request['user_id'])
                            ->where('publisher_id', $request['publisher_id'])->first();
            if($block){
                $block->delete();
                $msg     = $lang=="ar"?"تم حذف الحظر بنجاح":"sucessfull delete blocked";
            } else{
                $block                = new Block;
                $block->user_id       = $request['user_id'];
                $block->publisher_id  = $request['publisher_id'];
                $block->save();
                $msg                  = $lang=="ar"?"تم  الحظر بنجاح":"sucessfull blocked";
                publisher_log(
                    $request['user_id'],
                    ' لقد قمت بحظر  '.$block->publisher->display_name,
                    'you  blocked '.$block->publisher->display_name
                );
            }

            return response()->json(['status'=>true,'data' => ["blocks"=>""], 'msg' => $msg]);
        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // block-list
    public function block_list(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'user_id'   =>"required|exists:publishers,id",
        ]);

        if ($validator->passes()) {
            $lang    = $request['lang'];
            $user    = User::find($request['user_id']);
            $data    = $user->blockers->map(function ($blocker){
                return $this->responseBlock($blocker);
            });
            return response()->json(['status'=>true,'data' => ["blocks"=>$data], 'msg' => ""]);
        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // follow action
    public function followAction(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'user_id'        =>"required|exists:publishers,id",
            'publisher_id'   =>"required|exists:publishers,id",
        ]);

        if ($validator->passes()) {
            $lang    = $request['lang'];
            if($request['user_id'] == $request['publisher_id']){
                $msg     = $lang=="ar"?"لايمكن تبع نفسك":"can\'t follow your self";
                return response()->json(['status'=>false,'data' => ["blocks"=>""], 'msg' => $msg]);
            }
            $follow   = Follower::where('follower_id', $request['user_id'])
                ->where('follow_id', $request['publisher_id'])->first();
            if($follow){
                $follow->delete();
                $msg     = $lang=="ar"?"تم حذف التتبع بنجاح":"sucessfull delete follow";
            } else{
                $follow                = new Follower;
                $follow->follower_id   = $request['user_id'];
                $follow->follow_id    = $request['publisher_id'];
                $follow->save();
                $msg                  = $lang=="ar"?"تم  التتبع بنجاح":"sucessfull follow";
                $followUser           = User::find($request['publisher_id']);
                $followerUser         = User::find($request['user_id']);
                $followUser->notify(  new FollowNotify($followUser ,   $followerUser ));
                publisher_log(
                    $request['user_id'],
                    ' لقد قمت تتبع  '.$follow->follow->display_name,
                    'you  follow  '.$follow->follow->display_name
                );
            }

            return response()->json(['status'=>true,'data' => ["follows"=>""], 'msg' => $msg]);
        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // follow-list
    public function follow_list(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'user_id'   =>"required|exists:publishers,id",
        ]);

        if ($validator->passes()) {
            $lang    = $request['lang'];
            $user    = User::find($request['user_id']);
            $data    = $user->follows->map(function ($blocker){
                return $this->responseBlock($blocker);
            });
            return response()->json(['status'=>true,'data' => ["follows"=>$data], 'msg' => ""]);
        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // follower-list
    public function follower_list(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'user_id'   =>"required|exists:publishers,id",
        ]);

        if ($validator->passes()) {
            $lang    = $request['lang'];
            $user    = User::find($request['user_id']);
            $data    = $user->followers->map(function ($blocker){
                return $this->responseBlock($blocker);
            });
            return response()->json(['status'=>true,'data' => ["followers"=>$data], 'msg' => ""]);
        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // search
    public function searchPublisher(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'search'   =>"required",
        ]);
        if ($validator->passes()) {
            $lang     = $request['lang'];
            $blocks       = Block::buldBlockId($request['user_id']);
            $blockingMe   = Block::buldBlockerId($request['user_id']);
            $allBlocks    = array_merge($blocks, $blockingMe);
            $publishrs= User::whereNotIn('id', $allBlocks )
                            ->where('username','LIKE', '%' . $request['search'] . '%')
                            ->orWhere('display_name','LIKE', '%' . $request['search'] . '%')->get();
            $data    = $publishrs->map(function ($publisher){
                return $this->responsePublisherUser($publisher);
            });
            return response()->json(['status'=>true,'data' => ["publishers"=>$data], 'msg' => ""]);
        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // logout
    public function logout(Request $request){
        $validator=Validator::make($request->all(),[
            'user_id'   =>"required|exists:publishers,id",
        ]);

        if ($validator->passes()) {
            $lang             = $request['lang'];
            $user             = User::find($request['user_id']);
            $user->device_id  = null;
            $user->device_type= null;
            $user->update();
            $msg = $lang == "ar"? "تم تسجيل الخروج":"sucessfull logout";
            return response()->json(['status'=>true,'data' => ["publisher"=>""], 'msg' => $msg]);
        }
        else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // reset mail
    public function restPasswordMail(Request $request){
        $validator=Validator::make($request->all(),[
            'email'   =>"required|email|exists:publishers,email",
        ]);

        if ($validator->passes()) {
            $lang                    = $request['lang'];
            $user                    = User::where('email',$request['email'])->first();
            $user->temporay_password = Str::random(10);
            $user->update();
            \Mail::to($user)->send(new  \App\Mail\ResetPassword($user->username,  $user->temporay_password));
            $msg = $lang == "ar"? "تم ارسال الميل ":"sucessfull send mail ";
            return response()->json(['status'=>true,'data' => ["publisher"=>['temp'=>$user->temporay_password]], 'msg' => $msg]);
        }
        else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // check code whic send
    public function checkTemploaryPassword(Request $request){
        $validator=Validator::make($request->all(),[
            'email'          =>"required|email|exists:publishers,email",
            'temp_password'  =>"required"
        ]);

        if ($validator->passes()) {
            $lang                    = $request['lang'];
            $user                    = User::where('email',$request['email'])->first();
            if( $user->temporay_password != $request['temp_password'])
            {
                $msg = $lang == "ar"? "كود الدخول الموقت غير صحيح":"tempory code for login not sucesss";
                return response()->json(['status'=>false,'data' => ["publisher"=>['temp'=>""]], 'msg' => $msg]);
            }

            $msg = $lang == "ar"? "تم التاكد يرجع تغير الرقم السرى":"sucessfull verifed user pleaze change passowrd";
            return response()->json(['status'=>true,'data' => ["publisher"=>$this->responsePublisherUser($user)], 'msg' => $msg]);
        }
        else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

    // reset-password
    public function resetPassword(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'user_id'   =>"required|exists:publishers,id",
            'password'  =>"required|min:6|max:190"
        ]);

        if ($validator->passes()) {
            $lang               = $request['lang'];
            $user               = User::find($request['user_id']);
            if(is_null( $user->temporay_password)){
                $msg = $lang == "ar"? "كود الدخول لم يتم ارساله":"tempory code not send";
                return response()->json(['status'=>false,'data' => ["publisher"=>['temp'=>""]], 'msg' => $msg]);
            }
            $user->password     = bcrypt(convert2english($request['password']));
            $user->temporay_password =null ;
            $user->update();
            publisher_log(
                $request['user_id'],
                ' لقد قمت تغير كلمة المرور  ',
                'you change the password'
            );
            $msg = $lang == "ar"? "تم تغير كلمة المرور":"change the password sucessfull";
            return response()->json(['status'=>true,'data' => ["publisher"=>""], 'msg' => $msg]);
        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

}

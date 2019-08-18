<?php

namespace App\Http\Controllers\API;

use App\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use URL;
use Image;
use Validator;
use App\Models\Store as User;
use File;
class StoreController extends Controller
{
    /**********************
     *     helper
     * ********************
     * */

    /*
     * @optional take add flage to return valdation for add or edit
     * @return arry of valdation
     * */
    protected function publisherValidationAdd($publisher)
    {

    }

    protected function responseUser($user, $type=2)
    {
        $res["id"]              = $user->id;
        $res["store_name"]      = $user->store_name;
        $res["mobile"]          = $user->mobile;
        $res["email"]           = $user->email;
        $res["city"]            = $user->city;
        $image                  = is_null($user['image'])? "default_image.png" : $user['image'];
        $res['image']           = asset('uploads/publishers') . '/' . $image;
        $res['status']          = is_null($user['status'])?1:$user['status'];
        $res['verified']        = is_null($user['verified'])?1:$user['verified'];
        $res['type']            = $type;
        $res['storeType']       = $this->responseStoreType($user->StoreType);
        $res['notification_count']= $user->unreadNotifications()->count();
        return $res;
    }

    //response storeType
    protected  function  responseStoreType($storeType)
    {
        $res['name_ar'] = $storeType['name_ar'];
        $res['name_en'] = $storeType['name_en'];
        $res['icon']    = asset('uploads/storeTypes').'/'.$storeType->icon;
        return $res;

    }
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
            "store_name"       => 'required|min:2|max:190|unique:stores,store_name',
            'image'            => 'nullable|image',
            'mobile'           => 'required|min:2|max:190|unique:stores,mobile',
            'email'            => 'required|email|min:2|max:190|unique:stores,email',
            'city'             => 'required|min:2|max:190',
            'store_type_id'    => 'required|exists:store_types,id',
            'password'         => 'required|min:6|max:190',
            'device_id'        => 'required',
            'device_type'      => 'required|in:ios,android',
            'address'          => 'required'
        ]);
        if ($validator->passes()) {
            $user               = new User;
            $user->store_name   = $request['store_name'];
            $user->mobile       = $request['mobile'];
            $user->email        = convert2english($request['email']);
            $user->password     = bcrypt(convert2english($request['password']));
            $user->city         = $request['city'];
            $user->device_id    = $request['device_id'];
            $user->device_type  = $request['device_type'];
            $user->store_type_id= $request['store_type_id'] ;
            $user->address      = $request['address'] ;
            $user->save();
            return response()->json(
                [
                    
                    'status' => true,
                    'data' => ['store'=>$this->responseUser($user)],
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
            if(! auth('store')->attempt( ['email'=> convert2english($request['email']) ,'password' => convert2english($request['password'] ) ])){
                $msg = $request['lang'] == 'ar' ? ' كلمه المرور او الاميل غير صحيح.' : ' password or email is wrong.';
                return response()->json(['status'=>false,'msg'=>$msg]);
            }
            $user = auth('store')->user();

//            if($user->verified == 0){
//                $msg = $request['lang'] == 'ar' ? 'لم يتم تاكيد الحساب بعد.' : 'account not verfied .';
//                return response()->json(['status'=>false,'msg'=>$msg]);
//            }

            if($user->status == 0){
                $msg = $request['lang'] == 'ar' ? 'المستخدم محظور حاليا يمكن التواصل مع الاداره.' : ' user has been blocked can contact with adminstration.';
                return response()->json(['status'=>false,'msg'=>$msg]);
            }

            return response()->json(
                [
                    
                    'status' => true,
                    'data' => ['store'=>$this->responseUser($user)],
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
            'store_id'          => 'required|exists:stores,id',
        ]);
        if ($validator->passes()) {
            $user     = User::find($request['store_id']);

            return response()->json(
                [
                    
                    'status' => true,
                    'data' => ['store'=>$this->responseUser($user)],
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
            'store_id'          => 'required|exists:stores,id',
        ]);
        if ($validator->passes()) {
            $user           = User::find($request['store_id']);
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
            'user_id'          => 'required|exists:stores,id',
            "store_name"       => 'required|min:2|max:190|unique:stores,mobile,'.$request['user_id'],
            'image'            => 'nullable|image',
            'store_type_id'    => 'required|exists:store_types,id',
            'mobile'           => 'required|min:2|max:190|unique:stores,mobile,'.$request['user_id'],
            'email'            => 'required|email|min:2|max:190|unique:stores,email,'.$request['user_id'],
            'city'             => 'required|min:2|max:190',
            'address'          => 'required|min:2|max:190',

        ]);

        if ($validator->passes()) {

            $user     = User::find($request['user_id']);
            $user->store_name   = $request['store_name'];
            $user->mobile       = $request['mobile'];
            $user->email        = convert2english($request['email']);
            $user->city         = $request['city'];
            $user->address      = $request['address'];
            $user->store_type_id= $request['store_type_id'] ;
            if($request['image'])
            {
                $photo=$request->image;
                if($user->image != 'default_image.png') {
                    File::delete('uploads/stores/'.$user->image);
                }

                $name = date('d-m-y').time().rand().'.'.$photo->getClientOriginalExtension();
                Image::make($photo)->save('uploads/stores/'.$name);
                $user->image = $name;

            }
            $user->save();
            $msg = $request['lang'] == 'ar' ? 'تم تعديل الحساب' : 'account update .';
            return response()->json(
                [
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
            'store_id'                  =>'required|exists:stores,id',
            'password'                  =>'required|min:6|max:190',
            'old_password'              =>'required|min:6|max:190',

        ]);

        if ($validator->passes())
        {
            $request["password"]    = convert2english($request["password"] );
            $request["old_password"]= convert2english($request["old_password"] );
            $user = User::find($request['store_id']);
            if(\Hash::check($request['old_password'],$user->password)) {
                $user['password'] = bcrypt($request['password']);
                $user->save();
                $arr['id'] = $user['id'];
                $msg = $request['lang'] == 'ar' ? 'تم تغير كلمة السر بنجاح ':" password change successfull";
                return response()->json(['data' => "", 'msg' => $msg]);
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
            'store_id'   =>"required|exists:stores,id",
            'lang'      =>"required|in:ar,en"
        ]);

        if ($validator->passes()) {
            $lang    = $request['lang'];
            $user    = User::find($request["store_id"]);
            $user["lang"]=$lang;
            $user->update();
            $msg = $lang=="ar"?"تم تغير اللغه بنجاح":"sucessfull change the langue";
            return response()->json(['status'=>true,'data'=>$msg]);
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
            'user_id'   =>"required|exists:stores,id",
        ]);

        if ($validator->passes()) {
            $lang             = $request['lang'];
            $user             = User::find($request['user_id']);
            $user->device_id  = null;
            $user->device_type= null;
            $user->update();
            $msg = $lang == "ar"? "تم تسجيل الخروج":"sucessfull logout";
            return response()->json(['status'=>true,'data' => ["store"=>""], 'msg' => $msg]);
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
            'email'   =>"required|email|exists:stores,email",
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
            'email'          =>"required|email|exists:stores,email",
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
            return response()->json(['status'=>true,'data' => ["publisher"=>$this->responseUser($user)], 'msg' => $msg]);
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
            'user_id'   =>"required|exists:stores,id",
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

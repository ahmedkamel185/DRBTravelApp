<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use URL;
use Image;
use Validator;
use App\Models\Publisher as User;
use File;

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
    protected function publisherValidationAdd($publisher)
    {

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

        return $res;
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
                        'key' => 'success',
                        'status' => true,
                        'data' => ['publisher'=>$this->responseUser($user)],
                        'msg'=>""
                    ]
                );
        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['key' => 'fail','status' => false, 'msg' => $msg[0]]);
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
                 return response()->json(['key'=>'fail','status'=> false,'msg'=>$msg]);
             }
             $user = auth('publisher')->user();

             if($user->verified == 0){
                 $msg = $request['lang'] == 'ar' ? 'لم يتم تاكيد الحساب بعد.' : 'account not verfied .';
                 return response()->json(['key'=>'fail','status'=>false,'msg'=>$msg]);
             }

             if($user->status == 0){
                 $msg = $request['lang'] == 'ar' ? 'المستخدم محظور حاليا يمكن التواصل مع الاداره.' : ' user has been blocked can contact with adminstration.';
                 return response()->json(['key'=>'fail','status'=>false,'msg'=>$msg]);
             }

             return response()->json(
                 [
                     'key' => 'success',
                     'status' => true,
                     'data' => ['publisher' => $this->responseUser($user)],
                     'msg'=>""
                 ]
             );


         }else{
             foreach ((array)$validator->errors() as $key => $value){
                 foreach ($value as $msg){
                     return response()->json(['key' => 'fail','status' => false, 'msg' => $msg[0]]);
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
                     'key' => 'success',
                     'status' => true,
                     'data' => ['publisher'=>$this->responseUser($user)],
                     'msg'=>""
                 ]
             );


         }else{
             foreach ((array)$validator->errors() as $key => $value){
                 foreach ($value as $msg){
                     return response()->json(['key' => 'fail','status' => false, 'msg' => $msg[0]]);
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
                    return response()->json(['key' => 'fail','status' => false, 'msg' => $msg[0]]);
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
                return response()->json(['key' => 'success', 'status' => true, 'data' => "", 'msg' => $msg]);
            }else
            {
                $msg = $request['lang'] == 'ar' ? 'رقم السرى القديم غير صحيح ':"old password not correct";
                return response()->json(['key'=>'fail','status'=>false,'msg'=>$msg]);
            }
        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['key' => 'fail','status' => false, 'msg' => $msg[0]]);
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
            return response()->json(['key'=>'success','status'=>true,'data' => "", 'msg' => $msg]);
        }else{
            foreach ((array)$validator->errors() as $key => $value){
                foreach ($value as $msg){
                    return response()->json(['key' => 'fail','status' => false, 'msg' => $msg[0]]);
                }
            }
        }
    }

}

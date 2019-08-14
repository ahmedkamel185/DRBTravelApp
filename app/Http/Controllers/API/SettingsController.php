<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Models\Contact as Contact;

class SettingsController extends Controller
{
    //
    protected function responseSetting($setting)
    {


    }

    public function contact(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'contact_number'   => 'required|min:2|max:190|exists:publishers,mobile',
            'email'            => 'required|email|min:2|max:190|exists:publishers,email',
            'subject'          => 'required|min:2|max:190',
            'desc'             => 'required'
        ]);

        if ($validator->passes())
        {
          $contact = new Contact;
          $contact->contact_number   = $request['contact_number'];
          $contact->email            = $request['email'];
          $contact->subject          = $request['subject'];
          $contact->desc             = $request['desc'];
          $contact->save();
            $msg = $request['lang'] == 'ar' ? 'تم ارسال الرساله بنجاح.' : 'message sent success .';
            return response()->json(
                [
                    'status' => true,
                    'data'  => "",
                    'msg'   => $msg
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

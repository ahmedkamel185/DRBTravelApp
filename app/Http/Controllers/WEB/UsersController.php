<?php

namespace App\Http\Controllers\WEB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Publisher;
use App\Models\Trip;
use App\Notifications\Admin as AdminNotify;

class UsersController extends Controller
{
    //
    public function index()
    {

        return view('users.index')
            ->with('users', Publisher::all());
    }

    public function changeStatus(Request $request)

    {

        $user = Publisher::find($request->user_id);

        $user->status = $request->status;
        if($user->status = 0){
            send_FCM(
                $user,
                [
                    'title_ar'=> 'تم حظر حسابك',
                    'title_en'=> "account block",
                    'msg_ar'  => "حسابك تم حظره يمكن التواصل مع الاداره",
                    'msg_en'  => "admin block account can contact with adminstration",
                    'type'    => "block"
                ]
            );
        }
        $user->save();



        return response()->json(['success'=>'Status change status.']);

    }


    public function changeVerified(Request $request)

    {

        $user = Publisher::find($request->user_id);

        $user->verified = $request->verified;
        if( $user->verified){
            $user->notify(new AdminNotify(
                $user,
                'تم تاكيد حسابك من قبل المدير',
                'vervifed the account from admin'
            ));
        }

        $user->save();



        return response()->json(['success'=>'Status change verified.']);

    }
    
    public function show($id)
    {

        $publisher =  Publisher::find($id);
        $trips = $publisher->trips->all();

           return view('users.show')
               ->with('user',$publisher)
               ->with('trips',$trips)
               ;







    }
}

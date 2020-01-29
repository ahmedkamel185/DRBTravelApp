<?php

namespace App\Http\Controllers\WEB;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Publisher;
use App\Models\Store;
use App\Notifications\Admin as AdminNotify;

class NotificationsController extends Controller
{
    //
    public function index()
    {
        return view('notifications.index')->with('notifications', AdminNotification::all());
    }

    public function sendNotification($id)
    {
        $notifcation = AdminNotification::findOrFail($id);
        $users  = Publisher::all();
        $stores = Store::all();

        foreach ($users as $user) {
            $user->notify( new AdminNotify($user, $notifcation->title, $notifcation->desc));
        }

        foreach ($stores as $user) {
            $user->notify( new AdminNotify($user, $notifcation->title, $notifcation->desc));
        }
        
        session()->flash('success', 'Notification send success');
        return redirect()->route('notification.index');
    }

    public function editNotification($id)
    {
        $notification = AdminNotification::find($id);
        return view('notifications.edit')->with('notification', $notification);
    }

    public function updateNotification(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required|min:3|max:190',
            'desc' => 'required'
        ]);
        $notification = AdminNotification::find($id);
        $notification->title = $request->title;
        $notification->desc = $request->desc;
        $notification->save();
        session()->flash('success', 'Notification updated success');
        return redirect()->route('notification.index');

    }

    public function addNotification()
    {
        return view('notifications.add');
    }

    public function storeNotification(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|min:3|max:190',
            'desc' => 'required'
        ]);
        $notification = new AdminNotification;
        $notification->title = $request->title;
        $notification->desc = $request->desc;
        $notification->save();
        session()->flash('success', 'Notification Add success');
        return redirect()->route('notification.index');



    }


}

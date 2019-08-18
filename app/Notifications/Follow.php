<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Str;

class Follow extends Notification
{
    use Queueable;
    public $user;
    public $follower;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $follower)
    {
        //
        $this->user          = $user;
        $this->follower      = $follower;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }


    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toDatabase()
    {
        $user      = $this->follower;
        $title_ar  = " قام  {$user->display_name} بعمل متابعه لك" ;
        $title_en  = "{$this->user->display_name} follow you" ;
        $msg_ar    = "يمكن مشاهدة ملفه الشخصى";
        $msg_en    = 'can see profile';
        $image     = asset('uploads/publishers') . '/' . $user->image;
        $data = [
            'pid'      =>$this->follower->id,
            'type'    => "follow",
            'title_ar'=>$title_ar,
            'title_en'=>$title_en,
            'msg_ar'  =>$msg_ar,
            'msg_en'  =>$msg_en,
            'image'   => $image,
        ];
        //        send_FCM($this->user);
        send_FCM($this->user, $data);
        return $data;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

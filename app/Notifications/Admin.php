<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Str;

class Admin extends Notification
{
    use Queueable;
    public $user;
    public $msg_ar;
    public $msg_en;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $msg_ar ,$msg_en)
    {
        //
        $this->user   = $user;
        $this->msg_ar = $msg_ar;
        $this->msg_en = $msg_en;
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
        $title_ar  = "اشعار من الادمن " ;
        $title_en  = "notification from dasboard";
        $image     = asset('uploads/publishers') . '/' . "default_image.png";
        $data = [
            'pid'      =>-1,
            'type'    => "admin",
            'title_ar'=>$title_ar,
            'title_en'=>$title_en,
            'msg_ar'  =>$this->msg_ar,
            'msg_en'  =>$this->msg_en,
            'image'   => $image,
        ];
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

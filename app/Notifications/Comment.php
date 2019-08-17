<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Str;

class Comment extends Notification
{
    use Queueable;
    public $user;
    public $type;
    public $id;
    public $comment;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user , $type, $id, $comment)
    {
        //
        $this->user    = $user;
        $this->type    = $type;
        $this->id      = $id;
        $this->comment =$comment;
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
        $user      = $this->comment->user;
        $title_ar  = " قام  {$user->display_name} بتعليق على منشورك" ;
        $title_en  = "{$this->user->display_name} do the comment in post" ;
        $msg_ar    = Str::limit($this->comment->body, 20, ' (...)');
        $image     = asset('uploads/publishers') . '/' . $user->image;
        $data = [
            'pid'     =>$this->id,
            'type'    => $this->type,
            'title_ar'=>$title_ar,
            'title_en'=>$title_en,
            'msg_ar'  =>$msg_ar,
            'msg_en'  =>$msg_ar,
            'image'   => $image,
        ];
//        send_FCM($this->user);
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

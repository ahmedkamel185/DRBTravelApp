<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Str;

class Like extends Notification
{
    use Queueable;
    public $user;
    public $type;
    public $pid;
    public $like;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user , $type, $pid, $like)
    {
        //
        $this->user    = $user;
        $this->type    = $type;
        $this->pid      = $pid;
        $this->like    =$like;
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
    public function toDatabase( )
    {
        $user      = $this->like->user;
        $title_ar  = " قام  {$user->display_name} بالاعجاب  بمنشورك" ;
        $title_en  = "{$this->user->display_name} do  like to post" ;
        $msg_ar    = "يمكن مشاهدة كل الاعجاب";
        $msg_en    = 'can see all likes';
        $image     = asset('uploads/publishers') . '/' . $user->image;
        $data = [
            'pid'     =>$this->pid,
            'type'    => $this->type,
            'title_ar'=>$title_ar,
            'title_en'=>$title_en,
            'msg_ar'  =>$msg_ar,
            'msg_en'  =>$msg_en,
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

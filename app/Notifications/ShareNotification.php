<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Str;

class ShareNotification extends Notification
{
    use Queueable;
    public $user;
    public $sharer;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $sharer)
    {
        //
        $this->user     = $user;
        $this->sharer   = $sharer;
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
        $user      = $this->user;
        $title_ar  = " قام  {$this->sharer->display_name} قام بمشاركة منشورك " ;
        $title_en  = "{$this->sharer->display_name} share the post" ;
        $image     = asset('uploads/publishers') . '/' . $this->sharer->image;
        $data = [
            'pid'     =>$this->sharer->id,
            'type'    => 'sharer',
            'title_ar'=>$title_ar,
            'title_en'=>$title_en,
            'msg_ar'  =>$title_ar,
            'msg_en'  =>$title_en,
            'image'   => $image,
        ];
        send_FCM($user, $data);
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

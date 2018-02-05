<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class BanNotification extends Notification
{
    use Queueable;

    protected $ban;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($ban)
    {
        $this->ban = $ban;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->from(config('custom.site_email'), config('custom.site_name'))
            ->subject(trans('emails.got_banned_subject', ['site_name' => config('custom.site_name')]))
            ->greeting(trans('emails.verification_greetings'))
            ->line(trans('emails.got_banned_p1'))
            ->line(trans(trans('models.banned_by') . ': ' . $this->ban->bannedBy->name))
            ->line(trans(trans('models.reason') . ': ' . $this->ban->reason))
            ->line(trans(trans('general.date') . ': ' . $this->ban->created_at));
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

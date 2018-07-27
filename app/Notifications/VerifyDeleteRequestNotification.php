<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyDeleteRequestNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($code)
    {
        $this->code = $code;
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
                    ->subject(trans('emails.verify_delete_subject', ['site_name' => config('custom.site_name')]))
                    ->greeting(trans('emails.verify_delete_p1', ['name' => $notifiable->name]))
                    ->line(trans('emails.verify_delete_p2'))
                    ->line($this->code)
                    ->line(trans('emails.verify_delete_p3'))
                    ->action(trans('general.cancel'), route('front.userprofile.delete.cancel.show'))
                    ->line(trans('emails.verify_delete_p4'));
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

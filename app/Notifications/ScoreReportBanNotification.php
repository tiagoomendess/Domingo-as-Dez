<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ScoreReportBanNotification extends Notification
{
    use Queueable;

    protected $expirationDate;

    protected $reason;

    public function __construct($expirationDate, $reason)
    {
        $this->expirationDate = $expirationDate;
        $this->reason = $reason;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->from(config('custom.site_email'), config('custom.site_name'))
            ->subject(trans('emails.score_report_ban_subject'))
            ->greeting(trans('emails.general_greeting', ['name' => $notifiable->name]))
            ->line(trans('emails.score_report_ban_line_1', [
                'expiration_date' => $this->expirationDate,
                'reason' => $this->reason]))
            ->line(trans('emails.score_report_ban_line_2'))
            ->line(trans('emails.score_report_ban_line_3', ['site_email' => config('custom.site_email')]));
    }

    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}

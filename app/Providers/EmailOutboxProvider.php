<?php

namespace App\Providers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use App\EmailOutbox;

class EmailOutboxProvider extends ServiceProvider
{
    public function boot()
    {
        // Listen for emails before they are sent
        Event::listen(MessageSending::class, function (MessageSending $event) {
            try {
                $this->createOutboxItem($event);
            } catch (\Exception $e) {
                Log::error('Error saving email to outbox: ' . $e->getMessage());
            }
        });
    }

    public function register()
    {
        // Register any application services if necessary.
    }

    private function createOutboxItem(MessageSending $event) {
        $message = $event->message; // Swift_Message instance

        // Retrieve email details
        $from    = $message->getFrom();
        $to      = $message->getTo();
        $cc      = $message->getCc();
        $bcc     = $message->getBcc();
        $subject = $message->getSubject();
        $body    = $message->getBody();

        Log::info('Email sent to ' . json_encode($to) . ' with subject: ' . $subject);

        // Capture headers as an array and then encode to JSON
        $headers = [];
        foreach ($message->getHeaders()->getAll() as $header) {
            $headers[$header->getFieldName()] = $header->getFieldBody();
        }

        // Process attachments if available
        $attachments = [];
        foreach ($message->getChildren() as $child) {
            if ($child instanceof \Swift_Attachment) {
                $attachments[] = [
                    'filename'    => $child->getFilename(),
                    'contentType' => $child->getContentType(),
                ];
            }
        }

        // Save the email details to the outbox table
        EmailOutbox::create([
            'from'        => json_encode($from),
            'to'          => json_encode($to),
            'cc'          => json_encode($cc),
            'bcc'         => json_encode($bcc),
            'subject'     => $subject,
            'body'        => $body,
            'headers'     => json_encode($headers),
            'attachments' => json_encode($attachments),
        ]);
    }
}

<?php

namespace App\Mail;

use http\Exception;
use Illuminate\Mail\Mailable;

class ExceptionMail extends Mailable
{
    /** @var Request  */
    private $request;

    /** @var Exception */
    private $exception;

    public function __construct($request, $exception)
    {
        $this->request = $request;
        $this->exception = $exception;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $httpStatusCode = $this->exception->getStatusCode();

        return $this->from(config('custom.site_email'), config('app.name'))->subject("Erro $httpStatusCode no website")->view('emails.exception')->with([
            'request' => $this->request,
            'exception' => $this->exception
        ]);
    }
}

<?php

namespace App\Exceptions;

use App\Mail\ExceptionMail;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param Exception $exception
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $exception)
    {
        $supportedCodes = [403, 404, 500];
        $notifiableCodes = [500, 501, 503, 504];

        if ($this->isHttpException($exception)) {
            $statusCode = $exception->getStatusCode();
            if (in_array($statusCode, $notifiableCodes) && config('custom.send_exception_to_mail'))
                $this->sendExceptionToMail($request, $exception);

            if (in_array($statusCode, $supportedCodes)) {
                $vars = [
                    'request' => $request,
                    'exception' => $exception
                ];
                return response()->view('errors.' . $statusCode, $vars, $statusCode);
            }
        }

        return parent::render($request, $exception);
    }

    private function sendExceptionToMail($request, $exception): void
    {
        Mail::to(config('custom.exception_notification_email'))
            ->send(new ExceptionMail($request, $exception));
    }
}

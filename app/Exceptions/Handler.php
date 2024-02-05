<?php

namespace App\Exceptions;

use App\Mail\ExceptionMail;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

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
        $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 500;
        $supportedCodes = [403, 404, 500];

        if ($this->shouldSendMail($exception)) {
            try {
                $this->sendExceptionToMail($request, $exception);
            } catch (Exception $e) {
                Log::error("Tried to send exception to mail, but failed. Exception: " . $e->getMessage());
            }
        }

        if ($this->isHttpException($exception)) {
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

    private function shouldSendMail(Exception $exception): bool {
        if (!config('custom.send_exception_to_mail')) {
            return false;
        }

        $ignoreExceptions = [
            ValidationException::class,
            NotFoundHttpException::class,
            AccessDeniedHttpException::class,
            UnauthorizedHttpException::class,
            MethodNotAllowedHttpException::class,
            AuthenticationException::class,
            ModelNotFoundException::class,
            TokenMismatchException::class,
        ];

        if (Arr::first($ignoreExceptions, function ($value) use ($exception) {
            return $exception instanceof $value;
        })) {
            return false;
        }

        return true;
    }

    private function sendExceptionToMail($request, $exception)
    {
        Mail::to(config('custom.exception_notification_email'))
            ->send(new ExceptionMail($request, $exception));
    }
}

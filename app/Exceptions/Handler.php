<?php

namespace App\Exceptions;

use Throwable;
use App\Traits\ResponsableTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;

class Handler extends ExceptionHandler
{
    use ResponsableTrait;

    protected $levels = [
        //
    ];

    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if (!$request->expectsJson()) {
            return parent::render($request, $e);
        }

        if ($e instanceof ValidationException) {
            return $this->setStatusCode(statusCode: 422)
                ->setStatus(status: false)
                ->respondWithError(
                    message: $e->validator->errors()->first()
                );
        }

        if ($e instanceof MethodNotAllowedException){
            return $this->setStatusCode(statusCode: 405)
                ->setStatus(status: false)
                ->respondWithError(
                    message: $e->getMessage()
                );
        }

        if ($e instanceof NotFoundHttpException || $e instanceof ModelNotFoundException) {
            return $this->setStatus(status: false)->respondNotFound();
        }

        if ($e instanceof AuthenticationException || $e instanceof UnauthorizedException) {
            return $this->setStatus(status: false)
                ->respondUnauthorized(
                    message: $e->getMessage()
                );
        }

        return $this->setStatusCode(statusCode: 500)
            ->setStatus(status: false)
            ->respondWithError(message: $e->getMessage());
    }
}

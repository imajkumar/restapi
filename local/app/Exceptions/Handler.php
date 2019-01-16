<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;


class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
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
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {

        if ($exception instanceof \Tymon\JWTAuth\Exceptions\JWTException)
        {
            throw new Exception('Invalid Token!, please try with a valid token.');
        }
        if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException)
        {
            throw new Exception('Token is expired, please login first.');
        }
        if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException)
        {
            throw new Exception('Invalid Token!, please try with a valid token.');
        }
        if ($exception instanceof \Tymon\JWTAuth\Exceptions\TokenBlacklistedException)
        {
            throw new Exception('Token is Blacklisted, please login first.');
        }

        return response()->json(['success' => false, 'error' => $exception->getMessage(), 'code' => 401]);

        // return parent::render($request, $exception);
    }
}

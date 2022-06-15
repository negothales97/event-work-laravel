<?php

namespace App\Exceptions;

use App\Http\Responses\DefaultResponse;
use App\Services\Responses\InternalError;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(
            function (NotFoundHttpException $e) {
                return response(null, 404);
            },
            function (AuthorizationException $e) {
                return $this->authorizationException($e);
            },
            function (AuthenticationException $e) {
                return $this->authenticationException($e);
            }
        );
    }

    protected function authorizationException(AuthorizationException $exception)
    {
        $response = new DefaultResponse(
            null,
            false,
            [
                new InternalError(
                    $exception->getMessage(),
                    $exception->getCode()
                )
            ],
            403
        );
        return response()->json($response->toArray(), 403);
    }

    protected function authenticationException(AuthenticationException $exception)
    {
        $response = new DefaultResponse(
            null,
            false,
            [
                new InternalError(
                    'Você não tem autorização para continuar',
                    $exception->getCode()
                )
            ],
            401
        );
        return response()->json($response->toArray(), 401);
    }
}

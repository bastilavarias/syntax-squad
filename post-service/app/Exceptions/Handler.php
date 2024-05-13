<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render(
        $request,
        Throwable $e
    ) {
        if ($e instanceof NotFoundHttpException) {
                return customResponse()
                    ->data([])
                    ->message('Route not found.')
                ->failed(404)
                ->generate();
        }

        if ($e instanceof RouteNotFoundException) {
            return customResponse()
                ->data([])
                ->message('Route not found.')
                ->failed(404)
                ->generate();
        }

        if ($e instanceof ModelNotFoundException) {
            return customResponse()
                ->data([])
                ->message('The identifier you are querying does not exist.')
                ->failed(404)
                ->generate();
        }

        if ($e instanceof AuthorizationException) {
            return customResponse()
                ->data([])
                ->message('You do not have right to access this resource.')
                ->failed(403)
                ->generate();
        }

        return parent::render($request, $e);
    }
}

<?php

use App\Helpers\ApiResponse;
use App\Http\Middleware\ForceJsonResponse;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            ForceJsonResponse::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        // Handle Authentication Exception
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return ApiResponse::unauthorized('Invalid or expired token');
            }
            return null;
        });

        // Handle Validation Exception
        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return ApiResponse::validationError(
                    $e->errors(),
                    'The provided data is invalid'
                );
            }
            return null;
        });

        // Handle Not Found Exception
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return ApiResponse::notFound('The requested resource was not found');
            }
            return null;
        });

        // Handle Method Not Allowed Exception
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return ApiResponse::error('HTTP method not allowed', 405);
            }
            return null;
        });

        // Handle Access Denied Exception
        $exceptions->render(function (AccessDeniedHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return ApiResponse::forbidden('You do not have permission to access this resource');
            }
            return null;
        });

        // Handle other exceptions (Server Error)
        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                // Skip exceptions already handled above
                if (
                    $e instanceof AuthenticationException ||
                    $e instanceof ValidationException ||
                    $e instanceof NotFoundHttpException ||
                    $e instanceof MethodNotAllowedHttpException ||
                    $e instanceof AccessDeniedHttpException
                ) {
                    return null;
                }

                if (config('app.debug')) {
                    // In development environment
                    return ApiResponse::error(
                        $e->getMessage(),
                        500,
                        [
                            'file' => $e->getFile(),
                            'line' => $e->getLine(),
                            'trace' => collect($e->getTrace())->take(5)->toArray()
                        ]
                    );
                } else {
                    // In production environment
                    return ApiResponse::serverError('An internal server error occurred');
                }
            }
            return null;
        });
    })->create();

<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\RequestId;
use App\Http\Middleware\ForceJsonResponse;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Middleware\HandleCors;
use Illuminate\Http\Request;
use L5Swagger\L5SwaggerServiceProvider;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(RequestId::class);
        $middleware->append(ForceJsonResponse::class);
        $middleware->appendToGroup('api', HandleCors::class);
    })
    ->withProviders([
        RouteServiceProvider::class,
        L5SwaggerServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Throwable $e, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            // Validation
            if ($e instanceof ValidationException) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors'  => $e->errors(),
                ], 422);
            }

            // 401
            if ($e instanceof AuthenticationException) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            // 403
            if ($e instanceof AuthorizationException) {
                return response()->json(['message' => 'This action is unauthorized.'], 403);
            }

            // 404 (route или model binding)
            if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                return response()->json(['message' => 'Not Found.'], 404);
            }

            // 429
            if ($e instanceof ThrottleRequestsException) {
                return response()->json(['message' => 'Too Many Requests.'], 429);
            }

            // Other HTTP errors
            if ($e instanceof HttpExceptionInterface) {
                $status = $e->getStatusCode();
                return response()->json([
                    'message' => $e->getMessage() ?: \Symfony\Component\HttpFoundation\Response::$statusTexts[$status] ?? 'Error',
                ], $status);
            }

            // Fallback 500
            return response()->json(['message' => 'Server Error'], 500);
        });
    })->create();

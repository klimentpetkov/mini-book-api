<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontReport = [];
    protected $dontFlash = ['password', 'password_confirmation'];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($request->expectsJson()) {
            $status = method_exists($e, 'getStatusCode')
                ? $e->getStatusCode()
                : 500;

            return response()->json([
                'message'    => $e->getMessage() ?: 'Server Error',
                'code'       => $status,
                'request_id' => $request->header('X-Request-Id'),
            ], $status);
        }

        return parent::render($request, $e);
    }
}

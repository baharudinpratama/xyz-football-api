<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // 
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, $request) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated. Token missing or invalid.'
            ], 401);
        });

        $exceptions->render(function (NotFoundHttpException $e, $request) {
            return response()->json([
                'status' => 'error',
                'message' => 'Resource not found.',
                'error' => $e->getMessage()
            ], 404);
        });

        $exceptions->render(function (ValidationException $e, $request) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'error' => $e->errors(),
            ], 422);
        });
    })->create();

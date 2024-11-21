<?php

use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Exception $e, Request $request) {
            $e->getCode() == 0 || $e->getCode() == "23000" ? $code = 500 : $code = $e->getCode();
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => false,
                    'message' => $e->getCode() == 0 || $e->getCode() == "23000" ?
                        "something went wrong. Don't fret - just try again in a bit" : $e->getMessage()
                ], $code);
            }
            return $request->expectsJson();
        });
    })->create();


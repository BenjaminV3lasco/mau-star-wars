<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Inertia\ExceptionResponse;
use Inertia\Inertia;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [HandleInertiaRequests::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // 403, 404 y 503 se muestran con la página Error.vue del sitio en
        // lugar de la de Laravel. El resto de los errores sigue el flujo normal.
        Inertia::handleExceptionsUsing(function (ExceptionResponse $respuesta) {
            $estado = $respuesta->statusCode();

            if ($respuesta->request->isMethod('GET') && in_array($estado, [403, 404, 503], true)) {
                return $respuesta->render('Error', ['estado' => $estado])->withSharedData();
            }
        });
    })->create();

<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [\App\Http\Middleware\PruneExpiredRiders::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
        $exceptions->render(function (HttpExceptionInterface $exception, Request $request) {
            $status = $exception->getStatusCode();
            if ($status === 500 && app()->hasDebugModeEnabled()) {
                return null;
            }

            $messages = [
                403 => ['title' => 'Piekļuve liegta', 'message' => 'Tev nav atļauts veikt šo darbību.'],
                404 => ['title' => 'Lapa nav atrasta', 'message' => 'Meklētā lapa vai ieraksts nav pieejams.'],
                419 => ['title' => 'Sesija beigusies', 'message' => 'Lūdzu, atsvaidzini lapu un mēģini vēlreiz.'],
                429 => ['title' => 'Pārāk daudz pieprasījumu', 'message' => 'Lūdzu, uzgaidi un mēģini vēlreiz.'],
                500 => ['title' => 'Radās kļūda', 'message' => 'Neizdevās izpildīt pieprasījumu. Lūdzu, mēģini vēlāk.'],
                503 => ['title' => 'Serviss īslaicīgi nav pieejams', 'message' => 'Lūdzu, mēģini vēlreiz pēc brīža.'],
            ];

            if (! isset($messages[$status])) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json(['message' => $messages[$status]['message']], $status, $exception->getHeaders());
            }

            return response()->view('errors.status', [
                ...$messages[$status],
                'status' => $status,
            ], $status, $exception->getHeaders());
        });
    })->create();

<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn () => route('signin'));
        $middleware->alias([
            'super_admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, \Illuminate\Http\Request $request) {
            if ($e->getStatusCode() !== 403) return null;
            \Illuminate\Support\Facades\Log::warning('403 Forbidden intercepted globally', [
                'url'     => $request->fullUrl(),
                'user_id' => $request->user()?->id,
                'message' => $e->getMessage(),
            ]);
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
            $fallback = auth()->check() ? route('mess.index') : route('signin');
            $message  = $e->getMessage() ?: 'You do not have permission to perform this action.';
            return redirect($request->header('referer', $fallback))->with('error', $message);
        });
    })->create();

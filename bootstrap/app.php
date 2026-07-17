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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('web', \App\Http\Middleware\RedirectIfDriver::class);
        $middleware->trustProxies(at: '*');
        $middleware->validateCsrfTokens(except: [
            'api/deliveries/*/location',
            'api/driver/location',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (\Throwable $e) {
            try {
                \App\Models\ActivityLog::create([
                    'user_id' => auth()->id(),
                    'action' => 'System Error',
                    'description' => substr('Msg: ' . $e->getMessage() . "\nFile: " . $e->getFile() . ':' . $e->getLine() . "\nTrace: " . $e->getTraceAsString(), 0, 1000),
                ]);
            } catch (\Throwable $ex) {
                // Ignore database write failure
            }
        });
    })->create();

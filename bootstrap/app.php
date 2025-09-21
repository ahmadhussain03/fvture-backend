<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Cloud environment detection
        $environment = env('APP_ENV');
 
        switch ($environment) {
            case 'production':
                // Dokploy Configuration - matches nginx headers
                $middleware->trustProxies(
                    at: '*',
                    headers: Request::HEADER_X_FORWARDED_FOR |
                        Request::HEADER_X_FORWARDED_HOST |
                        Request::HEADER_X_FORWARDED_PORT |
                        Request::HEADER_X_FORWARDED_PROTO
                );
                break;
 
            default:
                // Local/Development Configuration
                $middleware->trustProxies(
                    at: ['127.0.0.1', '::1'],
                    headers: Request::HEADER_X_FORWARDED_FOR |
                        Request::HEADER_X_FORWARDED_PROTO
                );
        }
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

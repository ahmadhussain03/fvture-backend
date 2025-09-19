<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnlyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip this middleware for login and logout routes
        if ($request->routeIs('filament.admin.auth.*')) {
            return $next($request);
        }

        if (auth()->check() && auth()->user()->isAdmin()) {
            return $next($request);
        }

        // If user is logged in but not admin, logout and redirect
        if (auth()->check()) {
            auth()->logout();
            return redirect()->route('filament.admin.auth.login')
                ->with('error', 'Access denied. Admin privileges required.');
        }

        // User is not logged in, let Filament handle the redirect
        return $next($request);
    }
}

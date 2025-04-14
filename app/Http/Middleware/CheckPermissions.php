<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            if (count($request->get('anyPermissionArray')) > 0 && !auth()->user()->anyPermissions($request->get('anyPermissionArray'))) {
                return redirectBackWithError("Sorry! You are not authorize to visit the page!", 'dashboard');
            }

            if (count($request->get('allPermissionArray')) > 0 && !auth()->user()->allPermissions($request->get('allPermissionArray'))) {
                return redirectBackWithError("Sorry! You are not authorize to visit the page!", 'dashboard');
            }
        }

        return $next($request);
    }
}

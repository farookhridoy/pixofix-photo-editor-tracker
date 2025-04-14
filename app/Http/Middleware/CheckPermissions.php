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
            $anyPermissions = (array) $request->get('anyPermissionArray', []);
            $allPermissions = (array) $request->get('allPermissionArray', []);

            if (!empty($anyPermissions) && !auth()->user()->hasAnyPermission($anyPermissions)) {
                return redirectBackWithError("Sorry! You are not authorized to visit the page!", 'dashboard');
            }

            if (!empty($allPermissions) && !auth()->user()->hasAllPermissions($allPermissions)) {
                return redirectBackWithError("Sorry! You are not authorized to visit the page!", 'dashboard');
            }
        }

        return $next($request);
    }
}

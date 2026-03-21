<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/superadmin/login')->with('error', 'Please login first');
        }

        if (!auth()->user()->isSuperAdmin()) {
            auth()->logout();
            return redirect('/superadmin/login')->with('error', 'Unauthorized access. Super Admin privileges required.');
        }

        return $next($request);
    }
}

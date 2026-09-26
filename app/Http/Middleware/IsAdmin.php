<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // If not logged in on admin guard → send to admin login
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        // Logged in but role is not admin → kick out
        if (Auth::guard('admin')->user()->role !== 'admin') {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login')->withErrors(['email' => 'Access denied.']);
        }

        return $next($request);
    }
}
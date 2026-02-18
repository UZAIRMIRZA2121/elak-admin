<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Support\Facades\Auth;
use Brian2694\Toastr\Facades\Toastr;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (Auth::guard('admin')->check()) {
            return $next($request);
        } elseif (Auth::guard('vendor')->check()) {
            Toastr::error("unauthorized Access");
            return redirect()->route('vendor.dashboard');
        } elseif (Auth::guard('customer')->check()) {
            Toastr::error("unauthorized Access");
            return redirect()->route('customer.dashboard');
        } elseif (Auth::guard('client')->check()) {
            Toastr::error("unauthorized Access");
            return redirect()->route('client.dashboard');
        }
        return redirect()->route('admin.auth.login');
    }
}

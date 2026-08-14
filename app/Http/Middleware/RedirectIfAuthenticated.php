<?php

namespace App\Http\Middleware;

use App\Support\Redirects;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            return redirect(Redirects::homeFor($request->user()));
        }

        return $next($request);
    }
}

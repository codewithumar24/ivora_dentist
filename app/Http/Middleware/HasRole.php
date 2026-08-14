<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HasRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();

        if (! $user || ! $user->hasRole($roles)) {
            throw new HttpException(403, 'This area is limited to '.implode(' / ', $roles).' accounts.');
        }

        return $next($request);
    }
}

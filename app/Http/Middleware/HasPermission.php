<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HasPermission
{
    public function handle(Request $request, Closure $next, string ...$permissions)
    {
        $user = $request->user();

        if (! $user || ! $user->hasAnyPermission($permissions)) {
            throw new HttpException(403, 'Your account does not have the "'.implode(', ', $permissions).'" permission.');
        }

        return $next($request);
    }
}

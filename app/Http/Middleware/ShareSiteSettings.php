<?php

namespace App\Http\Middleware;

use App\Models\Service;
use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class ShareSiteSettings
{
    public function handle(Request $request, Closure $next)
    {
        View::share('settings', Setting::map());
        View::share('navServices', Cache::remember('nav.services', 3600,
            fn () => Service::active()->orderBy('sort_order')->take(8)->get(['name', 'slug', 'icon'])
        ));

        return $next($request);
    }
}

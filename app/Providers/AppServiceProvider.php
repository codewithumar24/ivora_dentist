<?php

namespace App\Providers;

use App\Services\SlotService;
use App\Services\SmsService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SlotService::class);
        $this->app->singleton(SmsService::class);
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Paginator::useTailwind();

        \Illuminate\Support\Facades\Blade::if('permission', function (string $slug) {
            return auth()->check() && auth()->user()->hasPermission($slug);
        });

        \Illuminate\Support\Facades\Blade::if('role', function (string ...$slugs) {
            return auth()->check() && auth()->user()->hasRole($slugs);
        });
    }
}

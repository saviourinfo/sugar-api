<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register pin.auth route middleware alias
        $router = $this->app['router'];
        $router->aliasMiddleware('pin.auth', \App\Http\Middleware\PinAuth::class);
    }
}

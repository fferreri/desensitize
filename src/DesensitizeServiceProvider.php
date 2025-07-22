<?php

namespace Waryor\Desensitize;

use Illuminate\Support\ServiceProvider;
use Waryor\Desensitize\Routing\Router;

class DesensitizeServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * This method is the modern way to replace Laravel's default router
     * with our custom implementation.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('router', function ($app) {
            return new Router($app['events'], $app);
        });
    }
}

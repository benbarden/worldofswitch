<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\TopRatedService;

class TopRatedServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('Services\TopRatedService', function($app) {
            return new TopRatedService();
        });
    }
}

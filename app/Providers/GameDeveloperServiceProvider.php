<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\GameDeveloperService;

class GameDeveloperServiceProvider extends ServiceProvider
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
        $this->app->bind('Services\GameDeveloperService', function($app) {
            return new GameDeveloperService();
        });
    }
}

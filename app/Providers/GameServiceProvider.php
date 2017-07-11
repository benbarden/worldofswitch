<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\GameService;

class GameServiceProvider extends ServiceProvider
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
        $this->app->bind('Services\GameService', function($app) {
            return new GameService();
        });
    }
}

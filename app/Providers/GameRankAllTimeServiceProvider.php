<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\GameRankAllTimeService;

class GameRankAllTimeServiceProvider extends ServiceProvider
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
        $this->app->bind('Services\GameRankAllTimeService', function($app) {
            return new GameRankAllTimeService();
        });
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\GameTitleHashService;

class GameTitleHashServiceProvider extends ServiceProvider
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
        $this->app->bind('Services\GameTitleHashService', function($app) {
            return new GameTitleHashService();
        });
    }
}

<?php

namespace App\Providers;

use Carbon\Carbon;
use App\FacebookConnection;
use Tests\Feature\FakeFacebook;
use App\FacebookInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (app()->environment('testing')) {
            $this->app->bind(FacebookInterface::class, function ($app) {
                return new FakeFacebook;
            });
        } else {
            $this->app->bind(FacebookInterface::class, function ($app) {
                return new FacebookConnection(config('app.facebook'));
            });
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::setLocale('ar');
    }
}

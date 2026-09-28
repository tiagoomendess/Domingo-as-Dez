<?php

namespace App\Providers;

use App\Services\SocialAuth\SocialLoginRegistry;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(155);

        View::composer(['auth.login', 'auth.register', 'auth.social'], function ($view) {
            $view->with('socialProviders', app(SocialLoginRegistry::class)->enabled());
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

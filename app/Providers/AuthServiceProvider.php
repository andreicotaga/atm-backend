<?php

namespace App\Providers;

use App\Cards;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a Cards instance or null. You're free to obtain
        // the Cards instance via an API token or any other method necessary.

        $this->app['auth']->viaRequest('api', function ($request)
        {
            return Cards::where('card_number', $request->input('card_number'))->first();
        });
    }
}

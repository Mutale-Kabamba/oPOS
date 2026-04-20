<?php

namespace App\Providers;

use App\Listeners\LogAuthActivity;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Login::class, [LogAuthActivity::class, 'handle']);
        Event::listen(Logout::class, [LogAuthActivity::class, 'handle']);
    }
}

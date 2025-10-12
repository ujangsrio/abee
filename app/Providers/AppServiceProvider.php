<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Slot;
use App\Observers\SlotObserver;

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
        Slot::observe(SlotObserver::class);
    }
}

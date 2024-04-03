<?php

namespace App\Providers;

use App\Models\Assistant;
use App\Observers\AssistantObserver;
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
        Assistant::observe(AssistantObserver::class);
    }
}

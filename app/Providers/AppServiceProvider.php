<?php

namespace App\Providers;

use App\Models\Assistant;
use App\Models\Message;
use App\Models\User;
use App\Observers\AssistantObserver;
use App\Observers\MessageObserver;
use App\Observers\UserObserver;
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
        User::observe(UserObserver::class);
        Message::observe(MessageObserver::class);
    }
}

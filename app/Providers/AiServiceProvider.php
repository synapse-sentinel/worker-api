<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\AI\ModelSelector;

class AiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ModelSelector::class, function () {
            return new ModelSelector();
        });
    }

    public function boot(): void
    {
        //
    }
}
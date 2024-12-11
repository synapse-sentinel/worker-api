<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use EchoLabs\PrismClient\Client as PrismClient;

class PrismServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PrismClient::class, function ($app) {
            return new PrismClient([
                'api_key' => config('services.prism.api_key'),
                'base_url' => config('services.prism.base_url'),
            ]);
        });
    }

    public function boot(): void
    {
        //
    }
}

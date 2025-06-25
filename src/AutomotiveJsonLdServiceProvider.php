<?php

namespace Bestfitcz\AutomotiveJsonLd;

use Illuminate\Support\ServiceProvider;

class AutomotiveJsonLdServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/automotive-json-ld.php', 'automotive-json-ld'
        );
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/automotive-json-ld.php' => config_path('automotive-json-ld.php'),
        ], 'config');
    }
}

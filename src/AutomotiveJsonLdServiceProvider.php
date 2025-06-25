<?php

namespace Bestfitcz\AutomotiveJsonLd;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Bestfitcz\AutomotiveJsonLd\Commands\AutomotiveJsonLdCommand;

class AutomotiveJsonLdServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('automotive-json-ld')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_automotive_json_ld_table')
            ->hasCommand(AutomotiveJsonLdCommand::class);
    }
}

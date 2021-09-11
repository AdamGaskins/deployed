<?php

namespace AdamGaskins\Deployed;

use AdamGaskins\Deployed\Commands\DeployedCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class DeployedServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('deployed')
            ->hasConfigFile()
            ->hasViews()
            ->hasCommand(DeployedCommand::class);
    }
}

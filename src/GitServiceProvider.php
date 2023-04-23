<?php

namespace IBroStudio\Git;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use IBroStudio\Git\Commands\GitCommand;

class GitServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-git')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-git_table')
            ->hasCommand(GitCommand::class);
    }
}

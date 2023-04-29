<?php

namespace IBroStudio\Git;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use IBroStudio\Git\Commands\GitCommand;

class GitServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-git')
            ->hasConfigFile();
            //->hasCommand(GitCommand::class);
    }
}

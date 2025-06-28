<?php

declare(strict_types=1);

namespace IBroStudio\Git;

use IBroStudio\DataObjects\Enums\GitProvidersEnum;
use IBroStudio\Git\Contracts\GitProviderConnectorContract;
use IBroStudio\Git\Integrations\Github\GithubConnector;
use IBroStudio\NeonConfig\Concerns\UseNeonConfig;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class GitServiceProvider extends PackageServiceProvider
{
    use UseNeonConfig;

    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-git')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->loadConfig();

        $this->bindContracts();
    }

    private function bindContracts(): void
    {
        $this->app->bind(
            GitProviderConnectorContract::class,
            function () {
                return collect([
                    GitProvidersEnum::GITHUB->value => new GithubConnector(
                        username: config('git.auth.github.username'),
                        token: config('git.auth.github.token'),
                    ),
                ]);
            }
        );

        $this->app->bind('git', function ($app) {
            return Git::use(config('git.default.provider'));
        });
    }

    private function loadConfig()
    {
        $this->handleNeon()->forConfig();
    }
}

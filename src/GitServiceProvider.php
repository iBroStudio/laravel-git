<?php

namespace IBroStudio\Git;

use IBroStudio\Git\Contracts\ChangelogContract;
use IBroStudio\Git\Contracts\GitProviderConnectorContract;
use IBroStudio\Git\Contracts\GitProviderContract;
use IBroStudio\Git\Contracts\GitProviderRepositoryContract;
use IBroStudio\Git\Contracts\GitProviderUserContract;
use IBroStudio\Git\Enums\GitProvidersEnum;
use IBroStudio\Git\GitProviders\Github\GithubProvider;
use IBroStudio\Git\GitProviders\Github\GithubRepository;
use IBroStudio\Git\GitProviders\Github\GithubUser;
use IBroStudio\Git\Integrations\Github\GithubConnector;
use IBroStudio\NeonConfig\Concerns\UseNeonConfig;
use Illuminate\Support\Facades\Config;
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
        $this->bindContracts();

        $this->loadConfig();
    }

    private function bindContracts(): void
    {
        $this->app->bind(
            GitProviderConnectorContract::class,
            function () {
                return collect([
                    GitProvidersEnum::GITHUB->value => new GithubConnector(
                        username: config('git.testing.github.username'),
                        token: config('git.testing.github.token'),
                    ),
                ]);
            }
        );

        app()->bind(
            GitProviderContract::class,
            function ($app) {
                return collect([
                    GitProvidersEnum::GITHUB->value => app(GithubProvider::class),
                ]);
            }
        );

        app()->bind(
            GitProviderRepositoryContract::class,
            function ($app, $params) {
                return collect([
                    GitProvidersEnum::GITHUB->value => app(GithubRepository::class, $params),
                ]);
            }
        );

        app()->bind(
            GitProviderUserContract::class,
            function ($app, $params) {
                return collect([
                    GitProvidersEnum::GITHUB->value => app(GithubUser::class, $params),
                ]);
            }
        );

        app()->bind(ChangelogContract::class, Changelog::class);
    }

    private function loadConfig()
    {
        if (! Config::has('github')) {
            Config::set('github', require getcwd().'/vendor/graham-campbell/github/config/github.php');
        }

        $this->handleNeon()->forConfig();
    }
}

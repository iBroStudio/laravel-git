<?php

namespace IBroStudio\Git;

use IBroStudio\Git\Commands\GitCommand;
use IBroStudio\Git\Contracts\ChangelogContract;
use IBroStudio\Git\Contracts\GitProviderContract;
use IBroStudio\Git\Contracts\GitProviderRepositoryContract;
use IBroStudio\Git\Contracts\GitProviderUserContract;
use IBroStudio\Git\Enums\GitProvidersEnum;
use IBroStudio\Git\GitProviders\Github\GithubProvider;
use IBroStudio\Git\GitProviders\Github\GithubRepository;
use IBroStudio\Git\GitProviders\Github\GithubUser;
use IBroStudio\NeonConfig\Concerns\UseNeonConfig;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Nette\Neon\Neon;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class GitServiceProvider extends PackageServiceProvider
{
    use UseNeonConfig;

    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-git')
            ->hasConfigFile()
            ->hasCommand(GitCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->bindContracts();

        $this->loadConfig();
    }

    private function bindContracts(): void
    {
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

        //$this->handle('git', 'gitbro.neon');
        $this->handleNeon()->forConfig();
        /*
                dd(
                    Neon::decodeFile(getcwd() . '/gitbro.neon')
                );
        //*/

        /*
         * ibrwork gitbro
         *
                if ($this->app->environment('testing')) {

                    if (! File::exists(__DIR__.'/../config/git.testing.php')) {
                        throw new \RuntimeException('git.testing.php is missing in config folder.');
                    }

                    Config::set('git.testing', require __DIR__.'/../config/git.testing.php');

                    Config::set(
                        'github.connections.main.token',
                        Config::get('git.testing.github_token')
                    );
                }
        */
    }
}

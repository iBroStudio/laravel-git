<?php

namespace IBroStudio\Git\Tests;

use GrahamCampbell\GitHub\GitHubServiceProvider;
use IBroStudio\DataRepository\DataRepositoryServiceProvider;
use IBroStudio\Git\GitServiceProvider;
use IBroStudio\PipedTasks\PipedTasksServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\LaravelData\LaravelDataServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            DataRepositoryServiceProvider::class,
            LaravelDataServiceProvider::class,
            GitServiceProvider::class,
            GitHubServiceProvider::class,
            PipedTasksServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        if (! File::exists(__DIR__.'/../config/git.testing.php')) {
            throw new \RuntimeException('git.testing.php is missing in config folder.');
        }

        Config::set('git.testing', require __DIR__.'/../config/git.testing.php');

        Config::set(
            'github.connections.main.token',
            Config::get('git.testing.github_token')
        );

        Process::path(config('git.testing.repository'))
            ->run('git restore . --worktree --staged')
            ->throw();

        Process::path(config('git.testing.repository'))
            ->run('git fetch origin --tags')
            ->throw();

        Process::path(config('git.testing.repository'))
            ->run('git pull origin main --rebase')
            ->throw();
    }
}

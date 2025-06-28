<?php

namespace IBroStudio\Git\Tests;

use IBroStudio\DataObjects\DataObjectsServiceProvider;
use IBroStudio\Git\GitServiceProvider;
use IBroStudio\Git\Repository;
use IBroStudio\Tasks\TasksServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Orchestra\Testbench\TestCase as Orchestra;
use Saloon\Laravel\SaloonServiceProvider;
use Spatie\LaravelData\LaravelDataServiceProvider;

class TestCase extends Orchestra
{
    public string $path;
    public Repository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'IBroStudio\\Git\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        $this->loadMigrationsFrom(__DIR__.'/../vendor/ibrostudio/laravel-tasks/database/migrations');

        $this->artisan('migrate')->run();
        /*
                $this->loadLaravelMigrations();

                $this->artisan('vendor:publish', [
                    '--provider' => 'Spatie\Activitylog\ActivitylogServiceProvider',
                    '--tag' => 'activitylog-migrations',
                ])->run();

                $this->artisan('migrate')->run();

                $this->artisan(PipedTasksInstallCommand::class)->run();
        */

        $this->path = config('git.testing.directory').'/'.config('git.testing.repository');
        $this->repository = Repository::open($this->path);
    }

    protected function getPackageProviders($app): array
    {
        return [
            DataObjectsServiceProvider::class,
            LaravelDataServiceProvider::class,
            GitServiceProvider::class,
            SaloonServiceProvider::class,
            TasksServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        Process::path(config('git.testing.directory').'/'.config('git.testing.repository'))
            ->run('git restore . --worktree --staged')
            ->throw();

        Process::path(config('git.testing.directory').'/'.config('git.testing.repository'))
            ->run('git fetch origin --tags')
            ->throw();

        Process::path(config('git.testing.directory').'/'.config('git.testing.repository'))
            ->run('git pull origin main --rebase')
            ->throw();
    }
}

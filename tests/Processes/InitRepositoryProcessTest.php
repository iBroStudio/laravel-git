<?php

declare(strict_types=1);

use IBroStudio\DataObjects\Enums\GitProvidersEnum;
use IBroStudio\DataObjects\ValueObjects\GitSshUrl;
use IBroStudio\Git\Dto\OwnerDto\AuthOwnerDto;
use IBroStudio\Git\Dto\RepositoryDto\ConfigDto\RemoteDto;
use IBroStudio\Git\Enums\GitRepositoryVisibilitiesEnum;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\AbstractCreateGithubRepositoryRequest;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\CreateGithubAuthRepositoryRequest;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\CreateGithubRepositoryFromTemplateRequest;
use IBroStudio\Git\Processes\InitRepositoryProcess;
use IBroStudio\Git\Repository;
use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

it('can run init repository process', function () {
    Saloon::fake([
        AbstractCreateGithubRepositoryRequest::class => MockResponse::fixture('github/create_authenticated_user_repository'),
        CreateGithubAuthRepositoryRequest::class => MockResponse::fixture('github/create_authenticated_user_repository'),
    ]);

    $repository = Repository::from([
        'name' => 'new-repo',
        'branch' => config('git.default.branch'),
        'owner' => AuthOwnerDto::from(['name' => config('git.auth.github.username')]),
        'provider' => GitProvidersEnum::GITHUB,
        'remote' => RemoteDto::from([
            'name' => config('git.default.remote'),
            'url' => GitSshUrl::build(GitProvidersEnum::GITHUB, config('git.auth.github.username'), 'new-repo'),
        ]),
        'localParentDirectory' => config('git.testing.directory'),
        'visibility' => GitRepositoryVisibilitiesEnum::PRIVATE,
    ]);

    File::deleteDirectory($repository->path);

    $process = InitRepositoryProcess::create(['payload' => $repository])->handle();

    expect($process->state)->toBe(ProcessStatesEnum::COMPLETED)
        ->and($process->tasks)->each(fn ($task) => $task->state->toBe(TaskStatesEnum::COMPLETED))
        ->and($process->payload->path)->toBeReadableDirectory();
});

it('can run init repository from a template process', function () {
    Saloon::fake([
        CreateGithubRepositoryFromTemplateRequest::class => MockResponse::fixture('github/create_authenticated_user_repository_from_template'),
    ]);

    $repository = Repository::from([
        'name' => 'new-repo',
        'template' => 'git@github.com:spatie/package-skeleton-laravel.git',
        'branch' => config('git.default.branch'),
        'owner' => AuthOwnerDto::from(['name' => config('git.auth.github.username')]),
        'provider' => GitProvidersEnum::GITHUB,
        'remote' => RemoteDto::from([
            'name' => config('git.default.remote'),
            'url' => GitSshUrl::build(GitProvidersEnum::GITHUB, config('git.auth.github.username'), 'new-repo'),
        ]),
        'localParentDirectory' => config('git.testing.directory'),
        'visibility' => GitRepositoryVisibilitiesEnum::PRIVATE,
    ]);

    File::deleteDirectory($repository->path);

    $process = InitRepositoryProcess::create(['payload' => $repository])->handle();

    expect($process->state)->toBe(ProcessStatesEnum::COMPLETED)
        ->and($process->tasks)->each(fn ($task) => $task->state->toBe(TaskStatesEnum::COMPLETED))
        ->and($process->payload->path)->toBeReadableDirectory();
});

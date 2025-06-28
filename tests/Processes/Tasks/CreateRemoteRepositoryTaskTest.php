<?php

declare(strict_types=1);

use IBroStudio\DataObjects\Enums\GitProvidersEnum;
use IBroStudio\DataObjects\ValueObjects\GitSshUrl;
use IBroStudio\Git\Dto\OwnerDto\AuthOwnerDto;
use IBroStudio\Git\Dto\RepositoryDto\ConfigDto\RemoteDto;
use IBroStudio\Git\Enums\GitRepositoryVisibilitiesEnum;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\CreateGithubAuthRepositoryRequest;
use IBroStudio\Git\Processes\Tasks\CreateRemoteRepositoryTask;
use IBroStudio\Git\Repository;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

it('can create a repository on a remote', function () {
    Saloon::fake([
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

    $task = CreateRemoteRepositoryTask::create(['processable_dto' => $repository])
        ->tap()
        ->handle($repository);

    expect($task->state)->toBe(TaskStatesEnum::COMPLETED);
});

<?php

declare(strict_types=1);

use IBroStudio\DataObjects\Enums\GitProvidersEnum;
use IBroStudio\DataObjects\ValueObjects\GitSshUrl;
use IBroStudio\Git\Dto\OwnerDto\AuthOwnerDto;
use IBroStudio\Git\Dto\RepositoryDto\ConfigDto\RemoteDto;
use IBroStudio\Git\Enums\GitRepositoryVisibilitiesEnum;
use IBroStudio\Git\Processes\Tasks\DirectoryMustNotExistTask;
use IBroStudio\Git\Repository;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use IBroStudio\Tasks\Exceptions\AbortTaskAndProcessException;

it('can ensure that a directory does not exist', function () {
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

    $task = DirectoryMustNotExistTask::create(['processable_dto' => $repository])
        ->tap()
        ->handle($repository);

    expect($task->state)->toBe(TaskStatesEnum::COMPLETED);
});

it('throws error if a directory exist', function () {

    DirectoryMustNotExistTask::create(['processable_dto' => $this->repository])
        ->tap()
        ->handle($this->repository);
})->throws(AbortTaskAndProcessException::class);

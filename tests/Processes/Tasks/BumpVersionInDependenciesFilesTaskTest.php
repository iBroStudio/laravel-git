<?php

declare(strict_types=1);

use IBroStudio\DataObjects\Enums\SemanticVersionEnum;
use IBroStudio\DataObjects\ValueObjects\DependenciesJsonFile;
use IBroStudio\Git\Dto\RepositoryDto\ReleaseDto;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\Releases\GetGithubLatestReleaseRequest;
use IBroStudio\Git\Processes\Tasks\BumpVersionInDependenciesFilesTask;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

it('can bump version in dependencies files', function () {
    Saloon::fake([
        GetGithubLatestReleaseRequest::class => MockResponse::fixture('github/get_repository_latest_release'),
    ]);

    $release = $this->repository->releases()->latest();
    $version = $release->version->increment(SemanticVersionEnum::PATCH);

    $task = $this->repository->task(BumpVersionInDependenciesFilesTask::class, ReleaseDto::from([
        'version' => $version,
        'previous' => $release->version,
    ]));

    expect($task->state)->toBe(TaskStatesEnum::COMPLETED);

    DependenciesJsonFile::collectionFromPath($task->processable_dto->path)
        ->each(function (DependenciesJsonFile $file) use ($version) {
            expect($file->version()->withoutPrefix())->toBe($version->withoutPrefix());
        });

    $this->repository->restore();
});

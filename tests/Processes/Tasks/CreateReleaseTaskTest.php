<?php

declare(strict_types=1);

use IBroStudio\DataObjects\ValueObjects\SemanticVersion;
use IBroStudio\Git\Dto\RepositoryDto\ReleaseDto;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\Releases\CreateGithubReleaseRequest;
use IBroStudio\Git\Processes\Tasks\CreateReleaseTask;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

it('can run create release task', function () {
    Saloon::fake([
        CreateGithubReleaseRequest::class => MockResponse::fixture('github/create_repository_release'),
    ]);

    $version = SemanticVersion::from('v0.0.23');
    $previous = SemanticVersion::from('v0.0.22');
    $releaseDto = ReleaseDto::from([
        'version' => $version,
        'previous' => $previous,
    ]);

    $task = $this->repository->task(CreateReleaseTask::class, $releaseDto);

    expect($task->state)->toBe(TaskStatesEnum::COMPLETED);

    $this->repository->restore();
});

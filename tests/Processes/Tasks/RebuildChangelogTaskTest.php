<?php

declare(strict_types=1);

use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\Releases\GetGithubReleasesRequest;
use IBroStudio\Git\Processes\Tasks\RebuildChangelogTask;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

it('can run rebuild CHANGELOG task', function () {
    Saloon::fake([
        GetGithubReleasesRequest::class => MockResponse::fixture('github/get_repository_releases'),
    ]);

    $task = $this->repository->task(RebuildChangelogTask::class, $this->repository);

    expect($task->state)->toBe(TaskStatesEnum::COMPLETED);
});

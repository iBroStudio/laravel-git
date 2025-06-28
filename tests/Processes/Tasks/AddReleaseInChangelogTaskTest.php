<?php

declare(strict_types=1);

use IBroStudio\DataObjects\Enums\SemanticVersionEnum;
use IBroStudio\Git\Dto\RepositoryDto\ReleaseDto;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\Releases\GetGithubLatestReleaseRequest;
use IBroStudio\Git\Processes\Tasks\AddReleaseInChangelogTask;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use Illuminate\Support\Facades\Process;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

it('can run changelog update task', function () {
    Process::fake([
        '*' => Process::result(
            output: 'Repository has changes',
        ),
    ]);

    Saloon::fake([
        GetGithubLatestReleaseRequest::class => MockResponse::fixture('github/get_repository_latest_release'),
    ]);

    $current = $this->repository->releases()->latest();
    $version = $current->version->increment(SemanticVersionEnum::PATCH);
    $releaseDto = ReleaseDto::from([
        'version' => $version,
        'previous' => $current->version,
    ]);

    $task = $this->repository->task(AddReleaseInChangelogTask::class, $releaseDto);

    expect($task->state)->toBe(TaskStatesEnum::COMPLETED);

    $this->repository->restore();
});

<?php

declare(strict_types=1);

use IBroStudio\DataObjects\Enums\SemanticVersionEnum;
use IBroStudio\Git\Dto\RepositoryDto\ReleaseDto;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\Releases\GetGithubLatestReleaseRequest;
use IBroStudio\Git\Processes\AddReleaseInChangelogProcess;
use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use Illuminate\Support\Facades\Process;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

it('can run changelog update process', function () {
    Process::fake([
        '*' => Process::result(
            output: 'Repository has changes',
        ),
    ]);

    Saloon::fake([
        GetGithubLatestReleaseRequest::class => MockResponse::fixture('github/get_repository_latest_release'),
    ]);

    $release = $this->repository->releases()->latest();
    $version = $release->version->increment(SemanticVersionEnum::PATCH);

    $process = $this->repository->process(AddReleaseInChangelogProcess::class, ReleaseDto::from([
        'version' => $version,
        'previous' => $release->version,
    ]));

    expect($process->state)->toBe(ProcessStatesEnum::COMPLETED)
        ->and($process->tasks)->each(fn ($task) => $task->state->toBe(TaskStatesEnum::COMPLETED));

    $this->repository->restore();
});

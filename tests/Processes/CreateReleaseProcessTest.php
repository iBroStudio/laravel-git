<?php

declare(strict_types=1);

use IBroStudio\DataObjects\ValueObjects\SemanticVersion;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\Releases\CreateGithubReleaseRequest;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\Releases\GetGithubLatestReleaseRequest;
use IBroStudio\Git\Processes\CreateReleaseProcess;
use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use Illuminate\Support\Facades\Process;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

it('can run create release process', function () {
    Process::fake([
        '*' => Process::result(
            output: 'Repository has changes',
        ),
    ]);

    Saloon::fake([
        GetGithubLatestReleaseRequest::class => MockResponse::fixture('github/get_repository_latest_release'),
        CreateGithubReleaseRequest::class => MockResponse::fixture('github/create_repository_release'),
    ]);

    $process = $this->repository->process(CreateReleaseProcess::class, [
        'version' => SemanticVersion::from('v0.0.23'),
        'previous' => SemanticVersion::from('v0.0.22'),
    ]);

    expect($process->state)->toBe(ProcessStatesEnum::COMPLETED)
        ->and($process->tasks)->each(fn ($task) => $task->state->toBe(TaskStatesEnum::COMPLETED));
});

<?php

declare(strict_types=1);

use IBroStudio\Git\Commit;
use IBroStudio\Git\Dto\RepositoryDto\CommitDto;
use IBroStudio\Git\Enums\CommitTypeEnum;
use IBroStudio\Git\Processes\CreateCommitProcess;
use IBroStudio\Tasks\Enums\ProcessStatesEnum;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use Illuminate\Support\Facades\Process;

it('can run commit process', function () {
    Process::fake();
    $payload = $this->repository->updateDto([
        'commit' => CommitDto::from(CommitTypeEnum::TEST, 'test commit '.Carbon\CarbonImmutable::now()->format('Y-m-d H:i:s')),
    ]);

    $commitMock = Mockery::mock(Commit::class, [$payload]);
    $commitMock
        ->shouldReceive('add')
        ->with($payload);

    $process = $this->repository->process(CreateCommitProcess::class, $payload);

    expect($process->state)->toBe(ProcessStatesEnum::COMPLETED)
        ->and($process->tasks)->each(fn ($task) => $task->state->toBe(TaskStatesEnum::COMPLETED));
});

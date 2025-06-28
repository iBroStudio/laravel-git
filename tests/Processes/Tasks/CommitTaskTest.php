<?php

declare(strict_types=1);

use IBroStudio\Git\Dto\RepositoryDto\CommitDto;
use IBroStudio\Git\Enums\CommitTypeEnum;
use IBroStudio\Git\Processes\Tasks\CommitTask;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use Illuminate\Support\Facades\Process;

it('can run commit task', function () {
    Process::fake([
        '*' => Process::result(
            output: 'Repository has changes',
        ),
    ]);

    $repository = $this->repository->updateDto([
        'commit' => CommitDto::from(CommitTypeEnum::TEST, 'test commit '.Carbon\CarbonImmutable::now()->format('Y-m-d H:i:s')),
    ]);
    /*
        $commitMock = Mockery::mock(Commit::class, [$repository]);
        $commitMock
            ->shouldReceive('add')
            ->with($repository->commit);
    */
    $task = $this->repository->task(
        CommitTask::class,
        $repository
    );

    expect($task->state)->toBe(TaskStatesEnum::COMPLETED);
});

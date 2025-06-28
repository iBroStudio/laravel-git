<?php

declare(strict_types=1);

use IBroStudio\Git\Dto\RepositoryDto\CommitDto;
use IBroStudio\Git\Enums\CommitTypeEnum;
use IBroStudio\Git\Processes\Tasks\PreCommitTask;
use IBroStudio\Tasks\Enums\TaskStatesEnum;

it('can run pre commit scripts', function () {
    $task = $this->repository->task(
        PreCommitTask::class,
        $this->repository->updateDto([
            'commit' => CommitDto::from(
                CommitTypeEnum::TEST,
                'test commit '.Carbon\CarbonImmutable::now()->format('Y-m-d H:i:s')
            ),
        ])
    );

    expect($task->state)->toBe(TaskStatesEnum::COMPLETED);
});

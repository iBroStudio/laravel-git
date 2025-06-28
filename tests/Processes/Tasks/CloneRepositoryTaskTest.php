<?php

declare(strict_types=1);

use IBroStudio\Git\Processes\Tasks\CloneRepositoryTask;
use IBroStudio\Tasks\Enums\TaskStatesEnum;
use Illuminate\Support\Facades\Process;

it('can clone a remote repository', function () {
    Process::fake([
        'git clone *' => 'Test "git clone output"',
    ]);

    $task = CloneRepositoryTask::create(['processable_dto' => $this->repository])
        ->tap()
        ->handle($this->repository);

    expect($task->state)->toBe(TaskStatesEnum::COMPLETED);
});

<?php

namespace IBroStudio\Git\Processes;

use IBroStudio\Git\Processes\Tasks\CloneRepositoryTask;
use IBroStudio\Git\Processes\Tasks\InitRemoteRepositoryTask;
use IBroStudio\PipedTasks\Process;

class InitRemoteRepositoryProcess extends Process
{
    protected array $tasks = [
        InitRemoteRepositoryTask::class,
        CloneRepositoryTask::class,
    ];
}

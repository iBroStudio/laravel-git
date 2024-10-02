<?php

namespace IBroStudio\Git\Processes;

use IBroStudio\Git\Processes\Tasks\CloneRepositoryTask;
use IBroStudio\Git\Processes\Tasks\DirectoryMustNotExistTask;
use IBroStudio\Git\Processes\Tasks\InitRemoteRepositoryTask;
use IBroStudio\PipedTasks\Process;

class InitRemoteRepositoryProcess extends Process
{
    protected array $tasks = [
        DirectoryMustNotExistTask::class,
        InitRemoteRepositoryTask::class,
        CloneRepositoryTask::class,
    ];
}

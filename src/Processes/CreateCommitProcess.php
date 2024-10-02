<?php

namespace IBroStudio\Git\Processes;

use IBroStudio\Git\Processes\Tasks\CommitTask;
use IBroStudio\Git\Processes\Tasks\FormatCodeTask;
use IBroStudio\Git\Processes\Tasks\TestCodeTask;
use IBroStudio\PipedTasks\Process;

class CreateCommitProcess extends Process
{
    protected array $tasks = [
        FormatCodeTask::class,
        TestCodeTask::class,
        CommitTask::class,
    ];
}

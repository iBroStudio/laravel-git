<?php

namespace IBroStudio\Git\Processes;

use IBroStudio\Git\Processes\Tasks\BumpVersionInComposerJsonTask;
use IBroStudio\Git\Processes\Tasks\PullTask;
use IBroStudio\Git\Processes\Tasks\PushTask;
use IBroStudio\PipedTasks\Process;

class BumpVersionInComposerJsonProcess extends Process
{
    protected array $tasks = [
        BumpVersionInComposerJsonTask::class,
        CreateCommitProcess::class,
        PullTask::class,
        PushTask::class,
    ];
}

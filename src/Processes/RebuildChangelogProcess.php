<?php

namespace IBroStudio\Git\Processes;

use IBroStudio\Git\Processes\Tasks\PullTask;
use IBroStudio\Git\Processes\Tasks\PushTask;
use IBroStudio\Git\Processes\Tasks\RebuildChangelogTask;
use IBroStudio\PipedTasks\Process;

class RebuildChangelogProcess extends Process
{
    protected array $tasks = [
        RebuildChangelogTask::class,
        CreateCommitProcess::class,
        PullTask::class,
        PushTask::class,
    ];
}

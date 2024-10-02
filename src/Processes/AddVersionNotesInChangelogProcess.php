<?php

namespace IBroStudio\Git\Processes;

use IBroStudio\Git\Processes\Tasks\AddVersionNotesInChangelogTask;
use IBroStudio\Git\Processes\Tasks\PullTask;
use IBroStudio\Git\Processes\Tasks\PushTask;
use IBroStudio\PipedTasks\Process;

class AddVersionNotesInChangelogProcess extends Process
{
    protected array $tasks = [
        AddVersionNotesInChangelogTask::class,
        CreateCommitProcess::class,
        PullTask::class,
        PushTask::class,
    ];
}

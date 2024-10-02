<?php

namespace IBroStudio\Git\Processes;

use IBroStudio\Git\Processes\Tasks\CreateRepositoryReleaseTask;
use IBroStudio\Git\Processes\Tasks\FetchTask;
use IBroStudio\Git\Processes\Tasks\PullTask;
use IBroStudio\PipedTasks\Process;

class CreateRepositoryReleaseProcess extends Process
{
    protected array $tasks = [
        PullTask::class,
        FetchTask::class,
        BumpVersionInComposerJsonProcess::class,
        AddVersionNotesInChangelogProcess::class,
        CreateRepositoryReleaseTask::class,
        FetchTask::class,
    ];
}

<?php

namespace IBroStudio\Git\Processes;

use IBroStudio\Git\Processes\Tasks\DeployTask;
use IBroStudio\Git\Processes\Tasks\TestCodeTask;
use IBroStudio\PipedTasks\Process;

class DeployProcess extends Process
{
    protected array $tasks = [
        TestCodeTask::class,
        DeployTask::class,
    ];
}

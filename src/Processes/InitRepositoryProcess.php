<?php

declare(strict_types=1);

namespace IBroStudio\Git\Processes;

use IBroStudio\Git\Processes\Tasks\CloneRepositoryTask;
use IBroStudio\Git\Processes\Tasks\CreateRemoteRepositoryTask;
use IBroStudio\Git\Processes\Tasks\DirectoryMustNotExistTask;
use IBroStudio\Git\Repository;
use IBroStudio\Tasks\DTO\ProcessConfigDTO;
use IBroStudio\Tasks\Models\Process;
use Illuminate\Support\Facades\Config;
use Parental\HasParent;

/**
 * @property Repository $payload
 */
class InitRepositoryProcess extends Process
{
    use HasParent;

    protected function getConfig(array $properties = []): ProcessConfigDTO
    {
        return parent::getConfig([
            'payload' => Repository::class,
            'tasks' => [
                DirectoryMustNotExistTask::class,
                CreateRemoteRepositoryTask::class,
                CloneRepositoryTask::class,
            ],
            'use_logs' => Config::get('git.log_git_processes'),
        ]);
    }
}

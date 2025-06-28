<?php

declare(strict_types=1);

namespace IBroStudio\Git\Processes;

use IBroStudio\Git\Dto\RepositoryDto\ReleaseDto;
use IBroStudio\Git\Processes\Tasks\BumpVersionInDependenciesFilesTask;
use IBroStudio\Git\Processes\Tasks\PullTask;
use IBroStudio\Git\Processes\Tasks\PushTask;
use IBroStudio\Git\Repository;
use IBroStudio\Tasks\Concerns\HasProcessableDto;
use IBroStudio\Tasks\DTO\ProcessConfigDTO;
use IBroStudio\Tasks\Models\Process;
use Illuminate\Support\Facades\Config;
use Parental\HasParent;

/**
 * @property ReleaseDto $payload
 */
class BumpVersionProcess extends Process
{
    use HasParent;
    use HasProcessableDto;

    protected function getConfig(array $properties = []): ProcessConfigDTO
    {
        return parent::getConfig([
            'tasks' => [
                BumpVersionInDependenciesFilesTask::class,
                CreateCommitProcess::class,
                PullTask::class,
                PushTask::class,
            ],
            'use_logs' => Config::get('git.log_git_processes'),
        ]);
    }

    protected function getProcessableDtoClass(): string
    {
        return Repository::class;
    }
}

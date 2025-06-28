<?php

declare(strict_types=1);

namespace IBroStudio\Git\Processes;

use IBroStudio\Git\Contracts\CommittableContract;
use IBroStudio\Git\Processes\Tasks\CommitTask;
use IBroStudio\Git\Processes\Tasks\PreCommitTask;
use IBroStudio\Git\Repository;
use IBroStudio\Tasks\Concerns\HasProcessableDto;
use IBroStudio\Tasks\DTO\ProcessConfigDTO;
use IBroStudio\Tasks\Models\Process;
use Illuminate\Support\Facades\Config;
use Parental\HasParent;

/**
 * @property CommittableContract $payload
 */
class CreateCommitProcess extends Process
{
    use HasParent;
    use HasProcessableDto;

    protected function getConfig(array $properties = []): ProcessConfigDTO
    {
        return parent::getConfig([
            'tasks' => [
                PreCommitTask::class,
                CommitTask::class,
            ],
            'use_logs' => Config::get('git.log_git_processes'),
        ]);
    }

    protected function getProcessableDtoClass(): string
    {
        return Repository::class;
    }
}

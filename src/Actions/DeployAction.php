<?php

namespace IBroStudio\Git\Actions;

use IBroStudio\Git\Enums\EnvironmentTypes;
use IBroStudio\Git\GitRepository;
use Illuminate\Support\Facades\Process;
use Spatie\QueueableAction\QueueableAction;

final class DeployAction
{
    use QueueableAction;

    public function execute(EnvironmentTypes $environmentTypes, GitRepository $repository): void
    {
        $configKey = 'git.scripts.deploy.'.$environmentTypes->value;

        if (! is_null(config($configKey))) {
            Process::path($repository->properties->path)
                ->run(config($configKey))
                ->throw();
        }
    }
}

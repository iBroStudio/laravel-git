<?php

namespace IBroStudio\Git\Actions;

use IBroStudio\Git\GitRepository;
use Illuminate\Support\Facades\Process;
use Spatie\QueueableAction\QueueableAction;

final class FormatCodeAction
{
    use QueueableAction;

    public function execute(GitRepository $repository): void
    {
        if (count(config('git.scripts.format-code'))) {
            foreach (config('git.scripts.format-code') as $script) {
                Process::path($repository->properties->path)
                    ->run($script)
                    ->throw();
            }
        }
    }
}

<?php

namespace IBroStudio\Git\Actions;

use IBroStudio\Git\GitRepository;
use Illuminate\Support\Facades\Process;
use Spatie\QueueableAction\QueueableAction;

final class TestCodeAction
{
    use QueueableAction;

    public function execute(GitRepository $repository): void
    {
        if (count(config('git.scripts.test-code'))) {
            foreach (config('git.scripts.test-code') as $script) {
                Process::path($repository->properties->path)
                    ->run($script)
                    ->throw();
            }
        }
    }
}

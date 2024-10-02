<?php

namespace IBroStudio\Git\Commands;

use Illuminate\Console\Command;

class GitCommand extends Command
{
    public $signature = 'laravel-git';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}

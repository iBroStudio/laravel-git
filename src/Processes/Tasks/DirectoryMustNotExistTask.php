<?php

declare(strict_types=1);

namespace IBroStudio\Git\Processes\Tasks;

use IBroStudio\Git\Repository;
use IBroStudio\Tasks\Contracts\PayloadContract;
use IBroStudio\Tasks\Exceptions\AbortTaskAndProcessException;
use IBroStudio\Tasks\Models\Task;
use Illuminate\Support\Facades\File;
use Parental\HasParent;

class DirectoryMustNotExistTask extends Task
{
    use HasParent;

    /**
     * @param  Repository  $payload
     */
    public function execute(PayloadContract $payload): PayloadContract|array
    {
        if (File::isDirectory($payload->path)) {
            throw new AbortTaskAndProcessException($this, "Directory '{$payload->path}' already exists.");
        }

        return $payload;
    }
}

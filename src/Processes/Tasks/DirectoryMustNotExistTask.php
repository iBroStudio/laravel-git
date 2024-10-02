<?php

namespace IBroStudio\Git\Processes\Tasks;

use Closure;
use IBroStudio\Git\Processes\Payloads\Contracts\RepositoryPayload;
use Illuminate\Support\Facades\File;

final readonly class DirectoryMustNotExistTask
{
    public function __invoke(RepositoryPayload $payload, Closure $next): mixed
    {
        if (File::isDirectory($payload->getRepositoryProperties()->path)) {
            throw new \RuntimeException("Directory '{$payload->getRepositoryProperties()->path}' already exists.");
        }

        return $next($payload);
    }
}

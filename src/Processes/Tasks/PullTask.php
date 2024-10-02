<?php

namespace IBroStudio\Git\Processes\Tasks;

use Closure;
use IBroStudio\Git\Processes\Payloads\Contracts\RepositoryPayload;

final readonly class PullTask
{
    public function __invoke(RepositoryPayload $payload, Closure $next): mixed
    {
        $payload->getRepository()->pull();

        return $next($payload);
    }
}

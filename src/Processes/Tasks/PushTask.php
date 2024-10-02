<?php

namespace IBroStudio\Git\Processes\Tasks;

use Closure;
use IBroStudio\Git\Processes\Payloads\Contracts\RepositoryPayload;

final readonly class PushTask
{
    public function __invoke(RepositoryPayload $payload, Closure $next): mixed
    {
        $payload->getRepository()->push();

        return $next($payload);
    }
}

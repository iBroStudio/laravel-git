<?php

namespace IBroStudio\Git\Processes\Tasks;

use Closure;
use IBroStudio\Git\Processes\Payloads\Contracts\RepositoryPayload;

final readonly class FetchTask
{
    public function __invoke(RepositoryPayload $payload, Closure $next): mixed
    {
        $payload->getRepository()->fetch();

        return $next($payload);
    }
}

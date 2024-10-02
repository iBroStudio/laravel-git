<?php

namespace IBroStudio\Git\Processes\Tasks;

use Closure;
use IBroStudio\Git\Actions\InitRemoteRepositoryAction;
use IBroStudio\Git\Processes\Payloads\Contracts\RepositoryPayload;

final readonly class InitRemoteRepositoryTask
{
    public function __construct(
        private InitRemoteRepositoryAction $action,
    ) {}

    public function __invoke(RepositoryPayload $payload, Closure $next): mixed
    {
        $payload->setRepositoryProperties(
            $this->action->execute($payload->getRepositoryProperties())
        );

        return $next($payload);
    }
}

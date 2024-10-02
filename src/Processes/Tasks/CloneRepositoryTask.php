<?php

namespace IBroStudio\Git\Processes\Tasks;

use Closure;
use IBroStudio\Git\Actions\CloneRepositoryAction;
use IBroStudio\Git\Processes\Payloads\Contracts\RepositoryPayload;

final readonly class CloneRepositoryTask
{
    public function __construct(
        private CloneRepositoryAction $action,
    ) {}

    public function __invoke(RepositoryPayload $payload, Closure $next): mixed
    {
        $payload->setRepository(
            $this->action->execute($payload->getRepositoryProperties())
        );

        return $next($payload);
    }
}

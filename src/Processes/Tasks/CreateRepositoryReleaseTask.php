<?php

namespace IBroStudio\Git\Processes\Tasks;

use Closure;
use IBroStudio\Git\Actions\CreateReleaseAction;
use IBroStudio\Git\Processes\Payloads\Contracts\ReleasePayload;

final readonly class CreateRepositoryReleaseTask
{
    public function __construct(
        private CreateReleaseAction $action,
    ) {}

    public function __invoke(ReleasePayload $payload, Closure $next): mixed
    {
        $this->action->execute(
            repository: $payload->getRepository(),
            version: $payload->getReleaseData()->version
        );

        return $next($payload);
    }
}

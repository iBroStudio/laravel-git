<?php

namespace IBroStudio\Git\Processes\Tasks;

use Closure;
use IBroStudio\Git\Actions\FormatCodeAction;
use IBroStudio\Git\Processes\Payloads\Contracts\CommitPayload;

final readonly class FormatCodeTask
{
    public function __construct(
        private FormatCodeAction $action,
    ) {}

    public function __invoke(CommitPayload $payload, Closure $next): mixed
    {
        $this->action->execute($payload->getRepository());

        return $next($payload);
    }
}

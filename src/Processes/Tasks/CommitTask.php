<?php

namespace IBroStudio\Git\Processes\Tasks;

use Closure;
use IBroStudio\Git\Actions\CommitAction;
use IBroStudio\Git\Data\GitCommitData;
use IBroStudio\Git\Processes\Payloads\Contracts\CommitPayload;

final readonly class CommitTask
{
    public function __construct(
        private CommitAction $action,
    ) {}

    public function __invoke(CommitPayload $payload, Closure $next): mixed
    {
        $commit = $this->action->execute(
            $payload->getCommitData(),
            $payload->getRepository()
        );

        if ($commit instanceof GitCommitData) {
            $payload->setCommitData($commit);
        }

        return $next($payload);
    }
}

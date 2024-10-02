<?php

namespace IBroStudio\Git\Processes\Tasks;

use Closure;
use IBroStudio\Git\Actions\TestCodeAction;
use IBroStudio\Git\Processes\Payloads\Contracts\CommitPayload;
use IBroStudio\PipedTasks\Exceptions\TaskExecutionFailedException;
use Illuminate\Process\Exceptions\ProcessFailedException;

final readonly class TestCodeTask
{
    public function __construct(
        private TestCodeAction $action,
    ) {}

    public function __invoke(CommitPayload $payload, Closure $next): mixed
    {
        try {
            $this->action->execute($payload->getRepository());
        } catch (ProcessFailedException $e) {
            throw new TaskExecutionFailedException($e->getMessage());
        }

        return $next($payload);
    }
}

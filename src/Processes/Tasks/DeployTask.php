<?php

namespace IBroStudio\Git\Processes\Tasks;

use Closure;
use IBroStudio\Git\Actions\DeployAction;
use IBroStudio\Git\Processes\Payloads\DeployPayload;
use IBroStudio\PipedTasks\Exceptions\TaskExecutionFailedException;
use Illuminate\Process\Exceptions\ProcessFailedException;

final readonly class DeployTask
{
    public function __construct(
        private DeployAction $action,
    ) {}

    public function __invoke(DeployPayload $payload, Closure $next): mixed
    {
        try {
            $this->action->execute(
                environmentTypes: $payload->getEnvironmentType(),
                repository: $payload->getRepository(),
            );
        } catch (ProcessFailedException $e) {
            throw new TaskExecutionFailedException($e->getMessage());
        }

        return $next($payload);
    }
}

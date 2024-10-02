<?php

namespace IBroStudio\Git\Processes\Tasks;

use Closure;
use IBroStudio\Git\Actions\CloneRepositoryAction;
use IBroStudio\Git\Processes\Payloads\Contracts\RepositoryPayload;

final readonly class WaitForTemplateCommitTask
{
    public function __invoke(RepositoryPayload $payload, Closure $next): mixed
    {
        retry([3000, 5000, 5000, 10000, 20000], function () use ($payload) {
            $commits = $payload->getRepositoryProperties()
                ->provider
                ->api()
                ->repo()
                ->commits()
                ->all(
                    $payload->getRepositoryProperties()->owner,
                    $payload->getRepositoryProperties()->name,
                    ['sha' => $payload->getRepositoryProperties()->branch]
                );

            if (count($commits) > 0) {
                return true;
            }

            throw new \RuntimeException;
        });

        return $next($payload);
    }
}

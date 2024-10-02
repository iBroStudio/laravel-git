<?php

namespace IBroStudio\Git\Processes\Tasks;

use Closure;
use IBroStudio\Git\Changelog;
use IBroStudio\Git\Data\GitCommitData;
use IBroStudio\Git\Enums\GitCommitTypes;
use IBroStudio\Git\Processes\Payloads\RebuildChangelogPayload;

final readonly class RebuildChangelogTask
{
    public function __invoke(RebuildChangelogPayload $payload, Closure $next): mixed
    {
        if (
            (new Changelog)
                ->bind($payload->getRepository())
                ->rebuild()
        ) {
            $payload->setCommitData(
                new GitCommitData(
                    type: GitCommitTypes::DOCS,
                    message: 'rebuild CHANGELOG'
                )
            );
        }

        return $next($payload);
    }
}

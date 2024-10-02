<?php

namespace IBroStudio\Git\Processes\Tasks;

use Closure;
use IBroStudio\Git\Actions\AddVersionNotesInChangelogAction;
use IBroStudio\Git\Data\GitCommitData;
use IBroStudio\Git\Enums\GitCommitTypes;
use IBroStudio\Git\Processes\Payloads\Contracts\ReleasePayload;

final readonly class AddVersionNotesInChangelogTask
{
    public function __construct(
        private AddVersionNotesInChangelogAction $action,
    ) {}

    public function __invoke(ReleasePayload $payload, Closure $next): mixed
    {
        if (
            $this->action->execute(
                $payload->getRepository(),
                $payload->getReleaseData()
            )
        ) {
            $payload->setCommitData(
                new GitCommitData(
                    type: GitCommitTypes::DOCS,
                    message: "add version {$payload->getReleaseData()->version->withoutPrefix()->value()} notes in CHANGELOG"
                )
            );
        }

        return $next($payload);
    }
}

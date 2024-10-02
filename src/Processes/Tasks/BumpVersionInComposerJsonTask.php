<?php

namespace IBroStudio\Git\Processes\Tasks;

use Closure;
use IBroStudio\DataRepository\ValueObjects\VersionedComposerJson;
use IBroStudio\Git\Actions\BumpVersionInComposerJsonAction;
use IBroStudio\Git\Data\GitCommitData;
use IBroStudio\Git\Enums\GitCommitTypes;
use IBroStudio\Git\Processes\Payloads\Contracts\BumpVersionPayload;

final readonly class BumpVersionInComposerJsonTask
{
    public function __construct(
        private BumpVersionInComposerJsonAction $action,
    ) {}

    public function __invoke(BumpVersionPayload $payload, Closure $next): mixed
    {
        if (
            $this->action->execute(
                composerJson: VersionedComposerJson::make(
                    $payload->getRepository()->properties->path.'/composer.json'
                ),
                version: $payload->getVersion()
            )
        ) {
            $payload->setCommitData(
                new GitCommitData(
                    type: GitCommitTypes::CHORE,
                    message: "bump version {$payload->getVersion()->withoutPrefix()->value()} in composer.json"
                )
            );
        }

        return $next($payload);
    }
}

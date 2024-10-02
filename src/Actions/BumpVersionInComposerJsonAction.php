<?php

namespace IBroStudio\Git\Actions;

use IBroStudio\DataRepository\ValueObjects\SemanticVersion;
use IBroStudio\DataRepository\ValueObjects\VersionedComposerJson;
use Spatie\QueueableAction\QueueableAction;

final class BumpVersionInComposerJsonAction
{
    use QueueableAction;

    public function execute(VersionedComposerJson $composerJson, SemanticVersion $version): bool
    {
        return $composerJson->version($version)->value() === $version->withoutPrefix()->value();
    }
}

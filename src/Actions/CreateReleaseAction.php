<?php

namespace IBroStudio\Git\Actions;

use IBroStudio\DataRepository\ValueObjects\SemanticVersion;
use IBroStudio\Git\Changelog;
use IBroStudio\Git\GitRepository;
use Spatie\QueueableAction\QueueableAction;

final class CreateReleaseAction
{
    use QueueableAction;

    public function execute(GitRepository $repository, SemanticVersion $version): bool
    {
        $repository
            ->release()
            ->create(
                version: $version,
                changelog: (new Changelog)->bind($repository)
            );

        return true;
    }
}

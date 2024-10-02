<?php

namespace IBroStudio\Git\Actions;

use IBroStudio\Git\Changelog;
use IBroStudio\Git\Data\GitReleaseData;
use IBroStudio\Git\GitRepository;
use Spatie\QueueableAction\QueueableAction;

final class AddVersionNotesInChangelogAction
{
    use QueueableAction;

    public function execute(GitRepository $repository, GitReleaseData $releaseData): bool
    {
        return (new Changelog)
            ->bind($repository)
            ->prepend($releaseData);
    }
}

<?php

namespace IBroStudio\Git\Actions;

use IBroStudio\Git\Data\GitCommitData;
use IBroStudio\Git\GitRepository;
use Spatie\QueueableAction\QueueableAction;

final class CommitAction
{
    use QueueableAction;

    public function execute(GitCommitData $commitData, GitRepository $repository): GitCommitData|bool
    {
        if (! $repository->hasChanges()) {
            return false;
        }

        return $repository
            ->commit()
            ->add($commitData);
    }
}

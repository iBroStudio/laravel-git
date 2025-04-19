<?php

namespace IBroStudio\Git\Actions;

use IBroStudio\Git\Data\RepositoryData;
use IBroStudio\Git\GitRepository;
use Spatie\QueueableAction\QueueableAction;

final class CloneRepositoryAction
{
    use QueueableAction;

    public function execute(RepositoryData $propertiesData): GitRepository
    {
        return retry([3000, 5000, 5000, 10000, 20000], function () use ($propertiesData) {
            return GitRepository::clone(
                ssh_url: $propertiesData->ssh_url,
                localParentDirectory: $propertiesData->localParentDirectory
            );
        });
    }
}

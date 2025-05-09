<?php

namespace IBroStudio\Git\Actions;

use IBroStudio\Git\Data\RepositoryData;
use Spatie\QueueableAction\QueueableAction;

final class InitRemoteRepositoryAction
{
    use QueueableAction;

    public function execute(RepositoryData $propertiesData): RepositoryData
    {
        $repository = $propertiesData
            ->provider
            ->repository($propertiesData)
            ->init();

        return $repository->properties();
    }
}

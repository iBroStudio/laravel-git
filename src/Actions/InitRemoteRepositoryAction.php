<?php

namespace IBroStudio\Git\Actions;

use IBroStudio\Git\Data\RepositoryPropertiesData;
use Spatie\QueueableAction\QueueableAction;

final class InitRemoteRepositoryAction
{
    use QueueableAction;

    public function execute(RepositoryPropertiesData $propertiesData): RepositoryPropertiesData
    {
        $repository = $propertiesData
            ->provider
            ->repository($propertiesData)
            ->init();

        return $repository->properties();
    }
}

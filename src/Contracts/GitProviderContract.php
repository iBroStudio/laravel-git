<?php

namespace IBroStudio\Git\Contracts;

use IBroStudio\Git\Data\RepositoryPropertiesData;

interface GitProviderContract
{
    public function api(): mixed;

    public function user(): GitProviderUserContract;

    public function repository(RepositoryPropertiesData $properties): GitProviderRepositoryContract;
}

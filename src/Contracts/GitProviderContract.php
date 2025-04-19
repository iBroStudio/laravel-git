<?php

namespace IBroStudio\Git\Contracts;

use IBroStudio\Git\Data\RepositoryData;

interface GitProviderContract
{
    public function api(): mixed;

    public function user(): GitProviderUserContract;

    public function repository(RepositoryData $properties): GitProviderRepositoryContract;
}

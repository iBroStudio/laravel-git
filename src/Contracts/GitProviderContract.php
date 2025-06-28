<?php

declare(strict_types=1);

namespace IBroStudio\Git\Contracts;

use IBroStudio\Git\Dto\RepositoryDto;

interface GitProviderContract
{
    public function api(): mixed;

    public function user(): GitProviderUserContract;

    public function repository(RepositoryDto $properties): GitProviderRepositoryContract;
}

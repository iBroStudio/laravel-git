<?php

namespace IBroStudio\Git\GitProviders;

use IBroStudio\Git\Contracts\GitProviderContract;
use IBroStudio\Git\Contracts\GitProviderRepositoryContract;
use IBroStudio\Git\Contracts\GitProviderUserContract;
use IBroStudio\Git\Data\RepositoryPropertiesData;
use IBroStudio\Git\Enums\GitProvidersEnum;

abstract class GitProvider implements GitProviderContract
{
    public function __construct(
        protected GitProvidersEnum $providerKey
    ) {}

    public function user(): GitProviderUserContract
    {
        return app(GitProviderUserContract::class, ['provider' => $this])
            ->get($this->providerKey->value);
    }

    public function repository(RepositoryPropertiesData $properties): GitProviderRepositoryContract
    {
        return app(GitProviderRepositoryContract::class, ['properties' => $properties])
            ->get($this->providerKey->value);
    }
}

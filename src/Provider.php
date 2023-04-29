<?php

namespace IBroStudio\Git;

use IBroStudio\Git\Contracts\ProviderContract;
use IBroStudio\ReleaseManager\Contracts\VersionManagerContract;
use IBroStudio\ReleaseManager\DtO\VersionData;

class Provider
{
    private function __construct(
        private ProviderContract $provider,
    ) {}

    public static function use(
        string $provider
    ): static {
        return new static(
            provider: app()->make($provider)
        );
    }

    public function repository()
    {
        return $this->provider->repository();
    }
}

<?php

namespace IBroStudio\Git;

use IBroStudio\Git\Contracts\GitProviderConnectorContract;
use IBroStudio\Git\Contracts\GitProviderContract;
use IBroStudio\Git\Enums\GitProvidersEnum;

class NewGitProvider
{
    final private function __construct(
        private GitProvidersEnum $provider,
        private GitProviderConnectorContract $api,
    ) {}

    public static function use(GitProvidersEnum $provider,): static
    {
        return new static(
            provider: $provider,
            api: app(GitProviderConnectorContract::class)
                ->get($provider->value),
        );
    }

    public function repositories()
    {
        return $this->api->repositories();
    }
}

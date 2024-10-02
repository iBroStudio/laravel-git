<?php

namespace IBroStudio\Git\GitProviders;

use IBroStudio\Git\Contracts\GitProviderContract;

abstract class GitProviderUser
{
    public function __construct(
        public readonly GitProviderContract $provider
    ) {}
}

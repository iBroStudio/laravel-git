<?php

namespace IBroStudio\Git\GitProviders;

use IBroStudio\Git\Contracts\GitProviderRepositoryContract;

abstract class GitProviderRelease
{
    public function __construct(public readonly GitProviderRepositoryContract $repository) {}
}

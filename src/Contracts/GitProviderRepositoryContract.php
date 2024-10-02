<?php

namespace IBroStudio\Git\Contracts;

use IBroStudio\Git\Data\RepositoryPropertiesData;
use IBroStudio\Git\Enums\GitRepositoryVisibilities;

interface GitProviderRepositoryContract
{
    public function properties(): RepositoryPropertiesData;

    public function init(): GitProviderRepositoryContract;

    public function initFromTemplate(): GitProviderRepositoryContract;

    public function get(): GitProviderRepositoryContract;

    public function visibility(GitRepositoryVisibilities $visibility): GitProviderRepositoryContract;

    public function delete(): bool;

    public function release(): GitReleaseContract;
}

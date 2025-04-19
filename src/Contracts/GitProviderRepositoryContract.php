<?php

namespace IBroStudio\Git\Contracts;

use IBroStudio\Git\Data\RepositoryData;
use IBroStudio\Git\Enums\GitRepositoryVisibilitiesEnum;

interface GitProviderRepositoryContract
{
    public function properties(): RepositoryData;

    public function init(): GitProviderRepositoryContract;

    public function initFromTemplate(): GitProviderRepositoryContract;

    public function get(): GitProviderRepositoryContract;

    public function visibility(GitRepositoryVisibilitiesEnum $visibility): GitProviderRepositoryContract;

    public function delete(): bool;

    public function release(): GitReleaseContract;
}

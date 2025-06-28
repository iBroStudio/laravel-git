<?php

declare(strict_types=1);

namespace IBroStudio\Git\Contracts;

use IBroStudio\Git\Dto\RepositoryDto;
use IBroStudio\Git\Enums\GitRepositoryVisibilitiesEnum;

interface GitProviderRepositoryContract
{
    public function properties(): RepositoryDto;

    public function init(): self;

    public function initFromTemplate(): self;

    public function get(): self;

    public function visibility(GitRepositoryVisibilitiesEnum $visibility): self;

    public function delete(): bool;

    public function release(): GitReleaseContract;
}

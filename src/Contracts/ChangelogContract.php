<?php

namespace IBroStudio\Git\Contracts;

use IBroStudio\DataRepository\ValueObjects\SemanticVersion;
use IBroStudio\Git\Data\GitReleaseData;
use IBroStudio\Git\GitRepository;
use Illuminate\Support\Collection;

interface ChangelogContract
{
    public function bind(GitRepository $repository): self;

    public function prepend(GitReleaseData $releaseData): bool;

    public function pick(SemanticVersion $version): ?Collection;

    public function rebuild(): bool;
}

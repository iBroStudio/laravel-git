<?php

namespace IBroStudio\Git\Contracts;

use IBroStudio\DataRepository\ValueObjects\SemanticVersion;
use IBroStudio\Git\Changelog;
use Illuminate\Support\Collection;

interface GitReleaseContract
{
    public function latest(): ?SemanticVersion;

    public function create(SemanticVersion $version, Changelog $changelog): SemanticVersion;

    public function all(): Collection;
}

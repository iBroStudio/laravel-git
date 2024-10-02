<?php

namespace IBroStudio\Git\Processes\Payloads\Contracts;

use IBroStudio\DataRepository\ValueObjects\SemanticVersion;
use IBroStudio\Git\Data\GitCommitData;
use IBroStudio\Git\GitRepository;

interface BumpVersionPayload
{
    public function getRepository(): ?GitRepository;

    public function getVersion(): SemanticVersion;

    public function setCommitData(GitCommitData $commitData): void;
}

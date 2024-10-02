<?php

namespace IBroStudio\Git\Processes\Payloads\Contracts;

use IBroStudio\Git\Data\GitCommitData;
use IBroStudio\Git\Data\GitReleaseData;
use IBroStudio\Git\GitRepository;

interface ReleasePayload
{
    public function getRepository(): ?GitRepository;

    public function getReleaseData(): GitReleaseData;

    public function setCommitData(GitCommitData $commitData): void;
}

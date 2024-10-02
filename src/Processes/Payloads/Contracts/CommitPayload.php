<?php

namespace IBroStudio\Git\Processes\Payloads\Contracts;

use IBroStudio\Git\Data\GitCommitData;
use IBroStudio\Git\GitRepository;

interface CommitPayload
{
    public function setCommitData(GitCommitData $commitData): void;

    public function getCommitData(): ?GitCommitData;

    public function getRepository(): ?GitRepository;
}

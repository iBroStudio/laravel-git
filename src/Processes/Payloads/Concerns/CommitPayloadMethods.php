<?php

namespace IBroStudio\Git\Processes\Payloads\Concerns;

use IBroStudio\Git\Data\GitCommitData;

trait CommitPayloadMethods
{
    public function setCommitData(GitCommitData $commitData): void
    {
        $this->commitData = $commitData;
    }

    public function getCommitData(): ?GitCommitData
    {
        return $this->commitData;
    }
}

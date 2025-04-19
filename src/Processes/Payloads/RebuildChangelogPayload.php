<?php

namespace IBroStudio\Git\Processes\Payloads;

use IBroStudio\Git\Data\GitCommitData;
use IBroStudio\Git\Data\RepositoryData;
use IBroStudio\Git\GitRepository;
use IBroStudio\Git\Processes\Payloads\Concerns\CommitPayloadMethods;
use IBroStudio\Git\Processes\Payloads\Concerns\RepositoryPayloadMethods;
use IBroStudio\Git\Processes\Payloads\Contracts\CommitPayload;
use IBroStudio\Git\Processes\Payloads\Contracts\RepositoryPayload;
use IBroStudio\PipedTasks\Payload;

class RebuildChangelogPayload implements CommitPayload, Payload, RepositoryPayload
{
    use CommitPayloadMethods;
    use RepositoryPayloadMethods;

    public function __construct(
        protected GitRepository $repository,
        protected ?GitCommitData $commitData = null,
        protected ?RepositoryData $repositoryProperties = null
    ) {}
}

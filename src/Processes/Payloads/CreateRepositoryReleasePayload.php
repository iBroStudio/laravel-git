<?php

namespace IBroStudio\Git\Processes\Payloads;

use IBroStudio\DataRepository\ValueObjects\SemanticVersion;
use IBroStudio\Git\Data\GitCommitData;
use IBroStudio\Git\Data\GitReleaseData;
use IBroStudio\Git\Data\RepositoryPropertiesData;
use IBroStudio\Git\GitRepository;
use IBroStudio\Git\Processes\Payloads\Concerns\CommitPayloadMethods;
use IBroStudio\Git\Processes\Payloads\Concerns\RepositoryPayloadMethods;
use IBroStudio\Git\Processes\Payloads\Contracts\BumpVersionPayload;
use IBroStudio\Git\Processes\Payloads\Contracts\CommitPayload;
use IBroStudio\Git\Processes\Payloads\Contracts\ReleasePayload;
use IBroStudio\Git\Processes\Payloads\Contracts\RepositoryPayload;
use IBroStudio\PipedTasks\Payload;

class CreateRepositoryReleasePayload implements BumpVersionPayload, CommitPayload, Payload, ReleasePayload, RepositoryPayload
{
    use CommitPayloadMethods;
    use RepositoryPayloadMethods;

    public function __construct(
        protected GitRepository $repository,
        protected GitReleaseData $releaseData,
        protected ?GitCommitData $commitData = null,
        protected ?RepositoryPropertiesData $repositoryProperties = null
    ) {}

    public function getReleaseData(): GitReleaseData
    {
        return $this->releaseData;
    }

    public function getVersion(): SemanticVersion
    {
        return $this->releaseData->version;
    }
}

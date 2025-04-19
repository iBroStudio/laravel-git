<?php

namespace IBroStudio\Git\Processes\Payloads;

use IBroStudio\Git\Data\RepositoryData;
use IBroStudio\Git\GitRepository;
use IBroStudio\Git\Processes\Payloads\Concerns\RepositoryPayloadMethods;
use IBroStudio\Git\Processes\Payloads\Contracts\RepositoryPayload;
use IBroStudio\PipedTasks\Payload;

class InitRemoteRepositoryPayload implements Payload, RepositoryPayload
{
    use RepositoryPayloadMethods;

    public function __construct(
        protected RepositoryData $repositoryProperties,
        protected ?GitRepository $repository = null
    ) {}
}

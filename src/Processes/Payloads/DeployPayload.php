<?php

namespace IBroStudio\Git\Processes\Payloads;

use IBroStudio\Git\Data\RepositoryData;
use IBroStudio\Git\Enums\EnvironmentTypes;
use IBroStudio\Git\GitRepository;
use IBroStudio\Git\Processes\Payloads\Concerns\EnvironmentPayloadMethods;
use IBroStudio\Git\Processes\Payloads\Concerns\RepositoryPayloadMethods;
use IBroStudio\Git\Processes\Payloads\Contracts\EnvironmentPayload;
use IBroStudio\Git\Processes\Payloads\Contracts\RepositoryPayload;

class DeployPayload implements EnvironmentPayload, RepositoryPayload
{
    use EnvironmentPayloadMethods;
    use RepositoryPayloadMethods;

    public function __construct(
        protected EnvironmentTypes $environmentType,
        protected GitRepository    $repository,
        protected ?RepositoryData  $repositoryProperties = null,
    ) {}
}

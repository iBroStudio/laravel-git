<?php

namespace IBroStudio\Git\Processes\Payloads\Contracts;

use IBroStudio\Git\Data\RepositoryPropertiesData;
use IBroStudio\Git\GitRepository;

interface RepositoryPayload
{
    public function setRepository(GitRepository $repository): void;

    public function getRepository(): ?GitRepository;

    public function setRepositoryProperties(RepositoryPropertiesData $repositoryProperties): void;

    public function getRepositoryProperties(): ?RepositoryPropertiesData;
}

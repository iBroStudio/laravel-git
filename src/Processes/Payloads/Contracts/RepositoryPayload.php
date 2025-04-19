<?php

namespace IBroStudio\Git\Processes\Payloads\Contracts;

use IBroStudio\Git\Data\RepositoryData;
use IBroStudio\Git\GitRepository;

interface RepositoryPayload
{
    public function setRepository(GitRepository $repository): void;

    public function getRepository(): ?GitRepository;

    public function setRepositoryProperties(RepositoryData $repositoryProperties): void;

    public function getRepositoryProperties(): ?RepositoryData;
}

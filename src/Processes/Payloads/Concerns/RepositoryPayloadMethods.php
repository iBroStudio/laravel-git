<?php

namespace IBroStudio\Git\Processes\Payloads\Concerns;

use IBroStudio\Git\Data\RepositoryData;
use IBroStudio\Git\GitRepository;

trait RepositoryPayloadMethods
{
    public function setRepository(GitRepository $repository): void
    {
        $this->repository = $repository;
    }

    public function getRepository(): ?GitRepository
    {
        return $this->repository;
    }

    public function setRepositoryProperties(RepositoryData $repositoryProperties): void
    {
        $this->repositoryProperties = $repositoryProperties;
    }

    public function getRepositoryProperties(): ?RepositoryData
    {
        return $this->repositoryProperties;
    }
}

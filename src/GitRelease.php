<?php

namespace IBroStudio\Git;

use IBroStudio\DataRepository\ValueObjects\SemanticVersion;
use Illuminate\Support\Collection;

class GitRelease
{
    protected GitRepository $repository;

    public function bind(GitRepository $repository): self
    {
        $this->repository = $repository;

        return $this;
    }

    public function all(): Collection
    {
        return $this->repository
            ->properties
            ->provider
            ->repository($this->repository->properties)
            ->release()
            ->all();
    }

    public function latest(): ?SemanticVersion
    {
        return $this->repository
            ->properties
            ->provider
            ->repository($this->repository->properties)
            ->release()
            ->latest();
    }

    public function create(SemanticVersion $version, Changelog $changelog): SemanticVersion
    {
        return $this->repository
            ->properties
            ->provider
            ->repository($this->repository->properties)
            ->release()
            ->create($version, $changelog);
    }
}

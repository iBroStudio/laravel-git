<?php

declare(strict_types=1);

namespace IBroStudio\Git\Integrations\Github\Resources\Repositories;

use IBroStudio\Git\Contracts\ReleaseResourceContract;
use IBroStudio\Git\Dto\RepositoryDto\ReleaseDto;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\Releases\CreateGithubReleaseRequest;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\Releases\GetGithubLatestReleaseRequest;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\Releases\GetGithubReleaseByTagRequest;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\Releases\GetGithubReleaseRequest;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\Releases\GetGithubReleasesRequest;
use IBroStudio\Git\Repository;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;

class GithubReleaseResource extends BaseResource implements ReleaseResourceContract
{
    public function __construct(
        readonly protected Connector $connector,
        public Repository $repository) {}

    public function all(): Collection|LazyCollection
    {
        return $this->connector->paginate(
            new GetGithubReleasesRequest($this->repository)
        )->collect();
    }

    public function get(int $release_id): ReleaseDto
    {
        return $this->connector->send(
            new GetGithubReleaseRequest($this->repository, $release_id)
        )->dtoOrFail();
    }

    public function create(ReleaseDto $releaseDto): ReleaseDto
    {
        return $this->connector->send(
            new CreateGithubReleaseRequest($this->repository, $releaseDto)
        )->dtoOrFail();
    }

    public function update()
    {
        // TODO: Implement update() method.
    }

    public function delete()
    {
        // TODO: Implement delete() method.
    }

    public function getByTag(string $tag): ReleaseDto
    {
        return $this->connector->send(
            new GetGithubReleaseByTagRequest($this->repository, $tag)
        )->dtoOrFail();
    }

    public function latest(): ReleaseDto
    {
        return $this->connector->send(
            new GetGithubLatestReleaseRequest($this->repository)
        )->dtoOrFail();
    }
}

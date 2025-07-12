<?php

declare(strict_types=1);

namespace IBroStudio\Git\Integrations\Github\Resources\Repositories;

use IBroStudio\Git\Contracts\OwnerDtoContract;
use IBroStudio\Git\Contracts\ReleaseResourceContract;
use IBroStudio\Git\Contracts\RepositoryResourceContract;
use IBroStudio\Git\Dto\OwnerDto\AuthOwnerDto;
use IBroStudio\Git\Dto\OwnerDto\OrganizationOwnerDto;
use IBroStudio\Git\Dto\OwnerDto\UserOwnerDto;
use IBroStudio\Git\Enums\GitRepositoryTopicsEnum;
use IBroStudio\Git\Integrations\Github\GithubConnector;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\GetGithubAuthRepositoriesRequest;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\GetGithubOrganizationRepositoriesRequest;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\GetGithubUserRepositoriesRequest;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\CreateGithubAuthRepositoryRequest;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\CreateGithubOrganizationRepositoryRequest;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\CreateGithubRepositoryFromTemplateRequest;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\GetGithubRepositoryRequest;
use IBroStudio\Git\Repository;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;

/**
 * @property GithubConnector $connector
 */
class GithubRepositoryResource extends BaseResource implements RepositoryResourceContract
{
    public function __construct(
        readonly protected Connector $connector,
        public OwnerDtoContract $ownerDto,
        public ?Repository $repository = null) {}

    /**
     * @return Collection<int, Repository>|LazyCollection<int, Repository>
     */
    public function all(): Collection|LazyCollection
    {
        $request = match (true) {
            $this->ownerDto instanceof AuthOwnerDto => new GetGithubAuthRepositoriesRequest,
            $this->ownerDto instanceof OrganizationOwnerDto => new GetGithubOrganizationRepositoriesRequest($this->ownerDto),
            $this->ownerDto instanceof UserOwnerDto => new GetGithubUserRepositoriesRequest($this->ownerDto),
        };

        return $this->connector->paginate($request)->collect();
    }

    public function byTopics(array $topics): Collection|LazyCollection
    {
        $topics = Arr::map($topics, fn (string|GitRepositoryTopicsEnum $topic): string => $topic instanceof GitRepositoryTopicsEnum ? $topic->value : $topic
        );

        return $this->all()
            ->filter(fn (Repository $repository) => count(array_intersect($repository->topics, $topics)) > 0);
    }

    public function get(): Repository
    {
        return $this->connector->send(
            new GetGithubRepositoryRequest($this->repository)
        )->dtoOrFail();
    }

    public function create(): Repository
    {
        $request = match (true) {
            ! is_null($this->repository?->template) => new CreateGithubRepositoryFromTemplateRequest($this->repository),
            $this->ownerDto instanceof AuthOwnerDto => new CreateGithubAuthRepositoryRequest($this->repository),
            $this->ownerDto instanceof OrganizationOwnerDto => new CreateGithubOrganizationRepositoryRequest($this->repository),
        };

        return $this->connector->send($request)->dtoOrFail();
    }

    public function update()
    {
        // TODO: Implement update() method.
    }

    public function delete()
    {
        // TODO: Implement delete() method.
    }

    public function tags()
    {
        // TODO: Implement tags() method.
    }

    public function releases(): ReleaseResourceContract
    {
        return new GithubReleaseResource(
            connector: $this->connector,
            repository: $this->repository
        );
    }
}

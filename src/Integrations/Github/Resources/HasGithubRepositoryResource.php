<?php

declare(strict_types=1);

namespace IBroStudio\Git\Integrations\Github\Resources;

use IBroStudio\Git\Contracts\OwnerDtoContract;
use IBroStudio\Git\Contracts\RepositoryResourceContract;
use IBroStudio\Git\Integrations\Github\Resources\Repositories\GithubRepositoryResource;
use IBroStudio\Git\Repository;
use Saloon\Http\Connector;

/**
 * @property Connector $connector
 * @property OwnerDtoContract $ownerDto
 */
trait HasGithubRepositoryResource
{
    public function repositories(?Repository $repository = null): RepositoryResourceContract
    {
        return new GithubRepositoryResource(
            connector: $this->connector,
            ownerDto: $this->ownerDto,
            repository: $repository
        );
    }
}

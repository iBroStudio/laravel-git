<?php

namespace IBroStudio\Git\Integrations\Github\Resources;

use IBroStudio\Contracts\Data\Hosting\ServerData;
use IBroStudio\Git\Data\RepositoryData;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\GetGithubRepositoriesRequest;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\GetGithubRepository;
use IBroStudio\Git\Integrations\Github\Requests\Users\GetGithubUser;
use IBroStudio\Upcloud\SDK\Requests\Servers\GetServers;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;
use Saloon\PaginationPlugin\Contracts\HasPagination;

class GithubRepository extends BaseResource
{
    /**
     * @return Collection<int, RepositoryData>|LazyCollection<int, RepositoryData>
     */
    public function all(): Collection|LazyCollection
    {
        return $this->connector->paginate(
            new GetGithubRepositoriesRequest
        )->collect();

        dd($items);

        return $this->connector->paginate(
            new GetGithubRepositoriesRequest
        )->collect();

        dd($this->connector->send(
            new GetGithubRepositoriesRequest
        )->json());

        dd($this->connector->paginate(
            new GetGithubRepositoriesRequest
        ));

        return $this->connector->paginate(
            new GetGithubRepositoriesRequest
        );
    }

    public function get(string $username, string $repository_name): RepositoryData
    {
        return $this->connector->send(
            new GetGithubRepository($username, $repository_name)
        )->dtoOrFail();
    }
}

<?php

declare(strict_types=1);

namespace IBroStudio\Git\Integrations\Github;

use IBroStudio\Git\Contracts\AuthResourceContract;
use IBroStudio\Git\Contracts\GitProviderConnectorContract;
use IBroStudio\Git\Contracts\OrganizationResourceContract;
use IBroStudio\Git\Contracts\RepositoryResourceContract;
use IBroStudio\Git\Contracts\UserResourceContract;
use IBroStudio\Git\Integrations\Github\Resources\GithubAuthResource;
use IBroStudio\Git\Integrations\Github\Resources\GithubOrganizationResource;
use IBroStudio\Git\Integrations\Github\Resources\GithubUserResource;
use IBroStudio\Git\Integrations\Github\Resources\Repositories\GithubRepositoryResource;
use IBroStudio\Git\Repository;
use Illuminate\Support\Str;
use Saloon\Http\Auth\BasicAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\HasPagination;
use Saloon\PaginationPlugin\PagedPaginator;
use Saloon\PaginationPlugin\Paginator;

class GithubConnector extends Connector implements GitProviderConnectorContract, HasPagination
{
    protected ?string $response = GithubResponse::class;

    public function __construct(
        public readonly string $username,
        public readonly string $token
    ) {}

    public function resolveBaseUrl(): string
    {
        return 'https://api.github.com';
    }

    public function paginate(Request $request): Paginator
    {
        return new class(connector: $this, request: $request) extends PagedPaginator
        {
            protected ?int $perPageLimit = 30;

            protected function isLastPage(Response $response): bool
            {
                if (is_null($header = $response->header('Link'))) {
                    return true;
                }

                $pages = Str::of($header)
                    ->split('/,/')
                    ->mapWithKeys(function (string $link) {
                        $split = Str::of($link)
                            ->split('/;/');

                        return [
                            Str::of($split->last())->match('/rel="(.*)"/')->value() => (int) Str::of($split->first())->match('/page=([0-9]+)/')->value(),
                        ];
                    });

                return $this->getCurrentPage() === $pages->get('last');
            }

            protected function getPageItems(Response $response, Request $request): array
            {
                return $response->dtoOrFail();
            }

            protected function applyPagination(Request $request): Request
            {
                $request->query()->add('page', $this->currentPage);

                if (isset($this->perPageLimit)) {
                    $request->query()->add('per_page', $this->perPageLimit);
                }

                return $request;
            }
        };
    }

    public function auth(): AuthResourceContract
    {
        return new GithubAuthResource($this);
    }

    public function organizations(?string $organization_name = null): OrganizationResourceContract
    {
        return new GithubOrganizationResource($this, $organization_name);
    }

    public function repository(Repository $repository): RepositoryResourceContract
    {
        return new GithubRepositoryResource(
            connector: $this,
            ownerDto: $repository->owner,
            repository: $repository
        );
    }

    public function user(string $user_name): UserResourceContract
    {
        return new GithubUserResource($this, $user_name);
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/vnd.github+json',
            'User-Agent' => $this->username,
            'X-GitHub-Api-Version' => '2022-11-28',
        ];
    }

    protected function defaultAuth(): BasicAuthenticator
    {
        return new BasicAuthenticator($this->username, $this->token);
    }
}

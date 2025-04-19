<?php

namespace IBroStudio\Git\Integrations\Github;

use IBroStudio\Git\Contracts\GitProviderConnectorContract;
use IBroStudio\Git\Integrations\Github\Resources\GithubRepository;
use IBroStudio\Git\Integrations\Github\Resources\GithubUser;
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
    protected ?string $response =  GithubResponse::class;

    public function __construct(
        public readonly string $username,
        public readonly string $token
    ) {}

    public function resolveBaseUrl(): string
    {
        return 'https://api.github.com';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/vnd.github+json',
            'User-Agent' => $this->username,
        ];
    }

    protected function defaultAuth(): BasicAuthenticator
    {
        return new BasicAuthenticator($this->username, $this->token);
    }

    public function paginate(Request $request): Paginator
    {
        return new class(connector: $this, request: $request) extends PagedPaginator
        {
            protected ?int $perPageLimit = 30;

            protected function isLastPage(Response $response): bool
            {
                $pages = Str::of($response->header('Link'))
                    ->split('/,/')
                    ->mapWithKeys(function (string $link) {
                        $split = Str::of($link)
                            ->split('/;/');
                        return [
                            Str::of($split->last())->match('/rel="(.*)"/')->value() =>
                                (int) Str::of($split->first())->match('/page=([0-9]+)/')->value()
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

    public function repositories(): GithubRepository
    {
        return new GithubRepository($this);
    }

    public function users(): GithubUser
    {
        return new GithubUser($this);
    }
}

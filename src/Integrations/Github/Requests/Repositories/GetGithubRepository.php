<?php

namespace IBroStudio\Git\Integrations\Github\Requests\Repositories;

use IBroStudio\Git\Data\RepositoryData;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetGithubRepository extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public readonly string $username,
        public readonly string $repository_name) {}

    public function resolveEndpoint(): string
    {
        return "/repos/{$this->username}/{$this->repository_name}";
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return RepositoryData::from($response);
    }
}

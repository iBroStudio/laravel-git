<?php

declare(strict_types=1);

namespace IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository;

use IBroStudio\Git\Repository;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetGithubRepositoryRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(public Repository $repository) {}

    public function resolveEndpoint(): string
    {
        return "/repos/{$this->repository->owner->name}/{$this->repository->name}";
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return Repository::from($response);
    }
}

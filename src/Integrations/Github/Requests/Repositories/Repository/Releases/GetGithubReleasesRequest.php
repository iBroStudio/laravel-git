<?php

declare(strict_types=1);

namespace IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\Releases;

use IBroStudio\Git\Dto\RepositoryDto\ReleaseDto;
use IBroStudio\Git\Repository;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class GetGithubReleasesRequest extends Request implements Paginatable
{
    protected Method $method = Method::GET;

    public function __construct(public Repository $repository) {}

    public function resolveEndpoint(): string
    {
        return "/repos/{$this->repository->owner->name}/{$this->repository->name}/releases";
    }

    public function createDtoFromResponse(Response $response): array
    {
        return ReleaseDto::collectFromGithub($response);
    }
}

<?php

declare(strict_types=1);

namespace IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\Releases;

use IBroStudio\Git\Dto\RepositoryDto\ReleaseDto;
use IBroStudio\Git\Repository;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetGithubReleaseByTagRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(
        public Repository $repository,
        public readonly string $tag) {}

    public function resolveEndpoint(): string
    {
        return "/repos/{$this->repository->owner->name}/{$this->repository->name}/releases/tags/{$this->tag}";
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return ReleaseDto::from($response);
    }
}

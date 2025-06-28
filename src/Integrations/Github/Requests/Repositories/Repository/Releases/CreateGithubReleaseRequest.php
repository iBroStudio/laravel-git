<?php

declare(strict_types=1);

namespace IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\Releases;

use IBroStudio\Git\Dto\RepositoryDto\ReleaseDto;
use IBroStudio\Git\Repository;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class CreateGithubReleaseRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(
        public Repository $repository,
        public ReleaseDto $releaseDto) {}

    public function resolveEndpoint(): string
    {
        return "/repos/{$this->repository->owner->name}/{$this->repository->name}/releases";
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return ReleaseDto::from($response);
    }

    protected function defaultBody(): array
    {
        return [
            'tag_name' => $this->releaseDto->version->value,
            'body' => $this->releaseDto->description,
        ];
    }
}

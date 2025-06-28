<?php

declare(strict_types=1);

namespace IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository;

use IBroStudio\Git\Repository;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

abstract class AbstractCreateGithubRepositoryRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(protected Repository $repository) {}

    public function createDtoFromResponse(Response $response): mixed
    {
        return Repository::from($response);
    }

    protected function defaultBody(): array
    {
        return [
            'name' => $this->repository->name,
            'private' => $this->repository->isPrivate(),
            'owner' => $this->repository->owner->name,
        ];
    }
}

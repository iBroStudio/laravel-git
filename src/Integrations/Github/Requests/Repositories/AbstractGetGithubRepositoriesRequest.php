<?php

declare(strict_types=1);

namespace IBroStudio\Git\Integrations\Github\Requests\Repositories;

use IBroStudio\Git\Repository;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

abstract class AbstractGetGithubRepositoriesRequest extends Request implements Paginatable
{
    protected Method $method = Method::GET;

    public function createDtoFromResponse(Response $response): array
    {
        return Repository::collectFromGithub($response);
    }
}

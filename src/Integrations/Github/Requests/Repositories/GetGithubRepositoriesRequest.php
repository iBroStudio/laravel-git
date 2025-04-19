<?php

namespace IBroStudio\Git\Integrations\Github\Requests\Repositories;

use IBroStudio\Git\Data\RepositoryData;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class GetGithubRepositoriesRequest extends Request implements Paginatable
{
    protected Method $method = Method::GET;

    public function __construct() {}

    public function resolveEndpoint(): string
    {
        return "/user/repos";
    }

    public function createDtoFromResponse(Response $response): array
    {
        return RepositoryData::collectFromGithub($response);
    }
}
// https://api.github.com/user/repos
// https://api.github.com/orgs/ORG/repos
// https://api.github.com/repos/OWNER/ ?

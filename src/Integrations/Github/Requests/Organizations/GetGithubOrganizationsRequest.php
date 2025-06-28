<?php

declare(strict_types=1);

namespace IBroStudio\Git\Integrations\Github\Requests\Organizations;

use IBroStudio\Git\Concerns\HasEndpointFromPromotedProperty;
use IBroStudio\Git\Dto\OrganizationDto;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\Paginatable;

class GetGithubOrganizationsRequest extends Request implements Paginatable
{
    use HasEndpointFromPromotedProperty;

    protected Method $method = Method::GET;

    public function __construct(public readonly string $endpoint) {}

    public function createDtoFromResponse(Response $response): array
    {
        return OrganizationDto::collectFromGithub($response);
    }
}

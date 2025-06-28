<?php

declare(strict_types=1);

namespace IBroStudio\Git\Integrations\Github\Requests\Organizations;

use IBroStudio\Git\Dto\OrganizationDto;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetGithubOrganizationRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(public readonly string $organization_name) {}

    public function resolveEndpoint(): string
    {
        return "/orgs/{$this->organization_name}";
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return OrganizationDto::from($response);
    }
}

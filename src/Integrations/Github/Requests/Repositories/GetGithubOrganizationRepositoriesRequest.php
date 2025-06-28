<?php

declare(strict_types=1);

namespace IBroStudio\Git\Integrations\Github\Requests\Repositories;

use IBroStudio\Git\Dto\OwnerDto\OrganizationOwnerDto;

class GetGithubOrganizationRepositoriesRequest extends AbstractGetGithubRepositoriesRequest
{
    public function __construct(public OrganizationOwnerDto $ownerDto) {}

    public function resolveEndpoint(): string
    {
        return "/orgs/{$this->ownerDto->name}/repos";
    }
}

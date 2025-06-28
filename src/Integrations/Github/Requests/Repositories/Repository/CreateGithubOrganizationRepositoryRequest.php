<?php

declare(strict_types=1);

namespace IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository;

class CreateGithubOrganizationRepositoryRequest extends AbstractCreateGithubRepositoryRequest
{
    public function resolveEndpoint(): string
    {
        return "/orgs/{$this->repository->owner->name}/repos";
    }
}

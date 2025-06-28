<?php

declare(strict_types=1);

namespace IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository;

class CreateGithubAuthRepositoryRequest extends AbstractCreateGithubRepositoryRequest
{
    public function resolveEndpoint(): string
    {
        return '/user/repos';
    }
}

<?php

declare(strict_types=1);

namespace IBroStudio\Git\Integrations\Github\Requests\Repositories;

class GetGithubAuthRepositoriesRequest extends AbstractGetGithubRepositoriesRequest
{
    public function resolveEndpoint(): string
    {
        return '/user/repos';
    }
}

<?php

declare(strict_types=1);

namespace IBroStudio\Git\Integrations\Github\Requests\Repositories;

use IBroStudio\Git\Dto\OwnerDto\UserOwnerDto;

class GetGithubUserRepositoriesRequest extends AbstractGetGithubRepositoriesRequest
{
    public function __construct(public UserOwnerDto $ownerDto) {}

    public function resolveEndpoint(): string
    {
        return "/users/{$this->ownerDto->name}/repos";
    }
}

<?php

declare(strict_types=1);

namespace IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository;

class CreateGithubRepositoryFromTemplateRequest extends AbstractCreateGithubRepositoryRequest
{
    public function resolveEndpoint(): string
    {
        return "/repos/{$this->repository->template->username}/{$this->repository->template->repository}/generate";
    }
}

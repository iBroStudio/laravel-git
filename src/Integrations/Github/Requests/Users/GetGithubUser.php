<?php

namespace IBroStudio\Git\Integrations\Github\Requests\Users;

use Saloon\Enums\Method;
use Saloon\Http\Request;

class GetGithubUser extends Request
{
    protected Method $method = Method::GET;

    public function __construct(public readonly string $username) {}

    public function resolveEndpoint(): string
    {
        return "/users/{$this->username}";
    }
}

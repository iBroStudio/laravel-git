<?php

declare(strict_types=1);

namespace IBroStudio\Git\Integrations\Github\Requests\Users;

use IBroStudio\Git\Dto\GitUserDto;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetGithubUserRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(public readonly string $user_name) {}

    public function resolveEndpoint(): string
    {
        return "/users/{$this->user_name}";
    }

    public function createDtoFromResponse(Response $response): mixed
    {
        return GitUserDto::from($response);
    }
}

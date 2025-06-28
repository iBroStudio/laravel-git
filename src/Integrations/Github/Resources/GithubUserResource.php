<?php

declare(strict_types=1);

namespace IBroStudio\Git\Integrations\Github\Resources;

use IBroStudio\Git\Contracts\OwnerDtoContract;
use IBroStudio\Git\Contracts\UserResourceContract;
use IBroStudio\Git\Dto\GitUserDto;
use IBroStudio\Git\Dto\OwnerDto\UserOwnerDto;
use IBroStudio\Git\Integrations\Github\Requests\Users\GetGithubUserRequest;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;

class GithubUserResource extends BaseResource implements UserResourceContract
{
    use HasGithubRepositoryResource;

    protected OwnerDtoContract $ownerDto;

    public function __construct(
        readonly protected Connector $connector,
        readonly public ?string $user_name = null)
    {
        $this->ownerDto = UserOwnerDto::from(['name' => $this->user_name]);
    }

    public function get(): GitUserDto
    {
        return $this->connector->send(
            new GetGithubUserRequest($this->user_name)
        )->dtoOrFail();
    }
}

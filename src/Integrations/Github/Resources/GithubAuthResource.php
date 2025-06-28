<?php

declare(strict_types=1);

namespace IBroStudio\Git\Integrations\Github\Resources;

use IBroStudio\Git\Contracts\AuthResourceContract;
use IBroStudio\Git\Contracts\OrganizationResourceContract;
use IBroStudio\Git\Contracts\OwnerDtoContract;
use IBroStudio\Git\Dto\GitUserDto;
use IBroStudio\Git\Dto\OwnerDto\AuthOwnerDto;
use IBroStudio\Git\Integrations\Github\Requests\Auth\GetGithubAuthRequest;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;

class GithubAuthResource extends BaseResource implements AuthResourceContract
{
    use HasGithubRepositoryResource;

    protected OwnerDtoContract $ownerDto;

    public function __construct(readonly protected Connector $connector)
    {
        $this->ownerDto = AuthOwnerDto::from(['name' => config('git.auth.github.username')]);
    }

    public function get(): GitUserDto
    {
        return $this->connector->send(
            new GetGithubAuthRequest
        )->dtoOrFail();
    }

    public function organizations(): OrganizationResourceContract
    {
        return new GithubOrganizationResource(
            connector: $this->connector,
            memberDto: AuthOwnerDto::from(['name' => config('git.auth.github.username')])
        );
    }
}

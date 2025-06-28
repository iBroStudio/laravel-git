<?php

declare(strict_types=1);

namespace IBroStudio\Git\Integrations\Github\Resources;

use IBroStudio\Git\Contracts\OrganizationResourceContract;
use IBroStudio\Git\Contracts\OwnerDtoContract;
use IBroStudio\Git\Dto\OrganizationDto;
use IBroStudio\Git\Dto\OwnerDto\AuthOwnerDto;
use IBroStudio\Git\Dto\OwnerDto\OrganizationOwnerDto;
use IBroStudio\Git\Integrations\Github\Requests\Organizations\GetGithubOrganizationRequest;
use IBroStudio\Git\Integrations\Github\Requests\Organizations\GetGithubOrganizationsRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Saloon\Http\BaseResource;
use Saloon\Http\Connector;

class GithubOrganizationResource extends BaseResource implements OrganizationResourceContract
{
    use HasGithubRepositoryResource;

    protected OwnerDtoContract $ownerDto;

    public function __construct(
        readonly protected Connector $connector,
        public ?string $organization_name = null,
        public ?OwnerDtoContract $memberDto = null)
    {
        if (! is_null($organization_name)) {
            $this->ownerDto = OrganizationOwnerDto::from(['name' => $this->organization_name]);
        }
    }

    /**
     * @return Collection<int, OrganizationDto>|LazyCollection<int, OrganizationDto>
     */
    public function all(): Collection|LazyCollection
    {
        return $this->connector->paginate(
            new GetGithubOrganizationsRequest($this->getEndpoint())
        )->collect();
    }

    public function get(): OrganizationDto
    {
        return $this->connector->send(
            new GetGithubOrganizationRequest($this->organization_name)
        )->dtoOrFail();
    }

    protected function getEndpoint(): string
    {
        return match (true) {
            $this->memberDto instanceof AuthOwnerDto => '/user/orgs',
            default => '/organizations',
        };
    }
}

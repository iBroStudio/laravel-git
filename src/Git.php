<?php

declare(strict_types=1);

namespace IBroStudio\Git;

use IBroStudio\DataObjects\Enums\GitProvidersEnum;
use IBroStudio\Git\Contracts\AuthResourceContract;
use IBroStudio\Git\Contracts\GitProviderConnectorContract;
use IBroStudio\Git\Contracts\OrganizationResourceContract;
use IBroStudio\Git\Contracts\RepositoryResourceContract;
use IBroStudio\Git\Contracts\UserResourceContract;
use Saloon\Http\Request;
use Saloon\Http\Response;

class Git
{
    final private function __construct(
        public readonly GitProvidersEnum $provider,
        private GitProviderConnectorContract $api,
    ) {}

    public static function use(GitProvidersEnum $provider): static
    {
        return new static(
            provider: $provider,
            api: app(GitProviderConnectorContract::class)
                ->get($provider->value),
        );
    }

    public function auth(): AuthResourceContract
    {
        return $this->api->auth();
    }

    public function organizations(?string $organization_name = null): OrganizationResourceContract
    {
        return $this->api->organizations($organization_name);
    }

    public function repository(Repository $repository): RepositoryResourceContract
    {
        return $this->api->repository($repository);
    }

    public function user(?string $user_name = null): UserResourceContract
    {
        return $this->api->user($user_name);
    }

    public function request(Request $request): Response
    {
        return $this->api->request($request);
    }
}

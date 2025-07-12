<?php

declare(strict_types=1);

namespace IBroStudio\Git\Contracts;

use IBroStudio\Git\Repository;
use Saloon\Http\Request;
use Saloon\Http\Response;

interface GitProviderConnectorContract
{
    public function auth(): AuthResourceContract;

    public function organizations(?string $organization_name = null): OrganizationResourceContract;

    public function repository(Repository $repository): RepositoryResourceContract;

    public function user(string $user_name): UserResourceContract;

    public function request(Request $request): Response;
}

<?php

declare(strict_types=1);

namespace IBroStudio\Git\Contracts;

interface AuthResourceContract
{
    public function organizations(): OrganizationResourceContract;

    public function repositories(): RepositoryResourceContract;
}

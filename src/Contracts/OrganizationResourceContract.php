<?php

declare(strict_types=1);

namespace IBroStudio\Git\Contracts;

use IBroStudio\Git\Dto\OrganizationDto;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

interface OrganizationResourceContract
{
    public function all(): Collection|LazyCollection;

    public function get(): OrganizationDto;

    public function repositories(): RepositoryResourceContract;
}

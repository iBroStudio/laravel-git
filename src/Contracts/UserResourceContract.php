<?php

declare(strict_types=1);

namespace IBroStudio\Git\Contracts;

interface UserResourceContract
{
    public function repositories(): RepositoryResourceContract;
}

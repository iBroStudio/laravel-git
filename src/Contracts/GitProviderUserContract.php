<?php

declare(strict_types=1);

namespace IBroStudio\Git\Contracts;

use IBroStudio\Git\Dto\GitUserDto;
use Illuminate\Support\Collection;

interface GitProviderUserContract
{
    public function infos(): GitUserDto;

    public function organizations(): Collection;
}

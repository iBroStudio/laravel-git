<?php

namespace IBroStudio\Git\Contracts;

use IBroStudio\Git\Data\GitUserInfosData;
use Illuminate\Support\Collection;

interface GitProviderUserContract
{
    public function infos(): GitUserInfosData;

    public function organizations(): Collection;
}

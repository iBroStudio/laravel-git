<?php

namespace IBroStudio\Git\Data;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class GitUserInfosData extends Data
{
    public function __construct(
        #[MapInputName('login')]
        public string $name,
        /** @var Collection<int, GitUserOrganizationData> */
        public Collection $organizations
    ) {}
}

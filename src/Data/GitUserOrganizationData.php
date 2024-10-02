<?php

namespace IBroStudio\Git\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class GitUserOrganizationData extends Data
{
    public function __construct(
        #[MapInputName('login')]
        public string $name,
    ) {}
}

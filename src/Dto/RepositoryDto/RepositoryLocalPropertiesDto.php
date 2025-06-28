<?php

declare(strict_types=1);

namespace IBroStudio\Git\Dto\RepositoryDto;

use Spatie\LaravelData\Data;

class RepositoryLocalPropertiesDto extends Data
{
    public function __construct(
        public array $core,
        public array $remote,
        public array $branch,
    ) {}
}

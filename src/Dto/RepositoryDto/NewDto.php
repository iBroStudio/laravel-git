<?php

declare(strict_types=1);

namespace IBroStudio\Git\Dto\RepositoryDto;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class NewDto extends Data
{
    public function __construct(
        public ReleaseDto|Optional $release,
    ) {}
}

<?php

declare(strict_types=1);

namespace IBroStudio\Git\Dto;

use IBroStudio\Git\Contracts\CreateRepositoryDtoContract;
use Spatie\LaravelData\Data;

class CreateGithubRepositoryDto extends Data implements CreateRepositoryDtoContract
{
    public function __construct(
        public string $name,
        public bool $private,
        public string $owner,
    ) {}
}

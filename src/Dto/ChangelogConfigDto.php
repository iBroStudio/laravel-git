<?php

declare(strict_types=1);

namespace IBroStudio\Git\Dto;

use Spatie\LaravelData\Data;

class ChangelogConfigDto extends Data
{
    public function __construct(
        public string $file,
        public array $header,
    ) {}
}

<?php

namespace IBroStudio\Git\Data;

use Spatie\LaravelData\Data;

class ChangelogConfigData extends Data
{
    public function __construct(
        public string $file,
        public array $header,
    ) {}
}

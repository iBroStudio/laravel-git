<?php

declare(strict_types=1);

namespace IBroStudio\Git\Dto\OwnerDto;

use IBroStudio\Git\Contracts\OwnerDtoContract;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class AuthOwnerDto extends Data implements OwnerDtoContract
{
    public function __construct(
        public string|Optional $name,
    ) {}
}

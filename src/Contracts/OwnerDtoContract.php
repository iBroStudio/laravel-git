<?php

declare(strict_types=1);

namespace IBroStudio\Git\Contracts;

use Spatie\LaravelData\Optional;

interface OwnerDtoContract
{
    public string|Optional $name { get; }
}

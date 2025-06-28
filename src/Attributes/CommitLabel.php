<?php

declare(strict_types=1);

namespace IBroStudio\Git\Attributes;

use Attribute;

#[Attribute]
class CommitLabel
{
    public function __construct(public string $text) {}
}

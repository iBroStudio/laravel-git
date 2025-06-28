<?php

declare(strict_types=1);

namespace IBroStudio\Git\Contracts;

use IBroStudio\Git\Dto\RepositoryDto\CommitDto;
use Spatie\LaravelData\Optional;

interface CommittableContract
{
    public CommitDto|Optional $commit { get; }
}

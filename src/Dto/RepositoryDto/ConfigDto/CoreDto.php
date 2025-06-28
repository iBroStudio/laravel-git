<?php

declare(strict_types=1);

namespace IBroStudio\Git\Dto\RepositoryDto\ConfigDto;

use IBroStudio\Git\Dto\RepositoryDto\RepositoryLocalPropertiesDto;
use Spatie\LaravelData\Data;

class CoreDto extends Data
{
    public function __construct(
        public int $repositoryformatversion,
        public bool $filemode,
        public bool $bare,
        public bool $logallrefupdates,
        public bool $ignorecase,
        public bool $precomposeunicode,
    ) {}

    public static function fromLocalProperties(RepositoryLocalPropertiesDto $properties): self
    {
        return self::from($properties->core);
    }
}

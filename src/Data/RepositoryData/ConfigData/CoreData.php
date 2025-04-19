<?php

namespace IBroStudio\Git\Data\RepositoryData\ConfigData;

use Spatie\LaravelData\Data;

class CoreData extends Data
{
    public function __construct(
        public int $repositoryformatversion,
        public bool $filemode,
        public bool $bare,
        public bool $logallrefupdates,
        public bool $ignorecase,
        public bool $precomposeunicode,
    ) {}
}

<?php

declare(strict_types=1);

namespace IBroStudio\Git\Concerns;

use IBroStudio\Git\Changelog;
use IBroStudio\Git\Commit;
use IBroStudio\Git\Release;

trait RepositorySubResources
{
    public function commits(): Commit
    {
        return new Commit($this);
    }

    public function releases(): Release
    {
        return new Release($this);
    }

    public function changelog(): Changelog
    {
        return new Changelog($this);
    }
}

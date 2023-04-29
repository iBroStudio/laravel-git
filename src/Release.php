<?php

namespace IBroStudio\Git;

use IBroStudio\Git\DtO\CommitData;
use IBroStudio\Git\Exceptions\GitException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class Release
{
    private static $instance;

    private function __construct(private Repository $repository) {}

    public static function getInstance(Repository $repository)
    {
        if (!self::$instance) {
            self::$instance = new self($repository);
        }

        return self::$instance;
    }

    public function all(): array
    {
        return $this->repository
            ->properties
            ->provider
            ->releases($this->repository)
            ->all();
    }
}

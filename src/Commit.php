<?php

namespace IBroStudio\Git;

use IBroStudio\Git\DtO\CommitData;
use IBroStudio\Git\Exceptions\GitException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class Commit
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

    public function last(): CommitData
    {
        $hash = Process::path($this->repository->properties->path)
            ->run('git rev-parse --verify HEAD')
            ->throw();

        $commit = Process::path($this->repository->properties->path)
            ->run("git show -s --pretty='format:%H;%s;%an;%ae;%at' {$hash->output()}")
            ->throw();

        return CommitData::from($commit->output());
    }
}

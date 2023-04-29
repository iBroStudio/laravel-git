<?php

namespace IBroStudio\Git;

use IBroStudio\Git\Exceptions\GitException;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class Tag
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

    public function get(): string
    {
        $process = Process::path($this->repository->properties->path)
            ->run('git describe --tags')
            ->throw();

        return $process->output();
    }
}

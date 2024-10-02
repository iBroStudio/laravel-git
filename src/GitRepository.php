<?php

namespace IBroStudio\Git;

use IBroStudio\DataRepository\ValueObjects\GitSshUrl;
use IBroStudio\Git\Data\RepositoryPropertiesData;
use IBroStudio\Git\Processes\InitRemoteRepositoryProcess;
use IBroStudio\Git\Processes\Payloads\InitRemoteRepositoryPayload;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

class GitRepository
{
    public function __construct(
        public RepositoryPropertiesData $properties
    ) {}

    public static function init(
        InitRemoteRepositoryProcess $process,
        InitRemoteRepositoryPayload $payload): GitRepository
    {
        return $process
            ->run($payload)
            ->getRepository();
    }

    public static function open(string $path): GitRepository
    {
        return new self(
            RepositoryPropertiesData::from(realpath($path))
        );
    }

    public static function clone(GitSshUrl $ssh_url, string $localParentDirectory): GitRepository
    {
        $localParentDirectory = realpath($localParentDirectory);
        $path = $localParentDirectory.'/'.$ssh_url->repository();

        Process::path($localParentDirectory)
            ->run("git clone {$ssh_url->value()}")
            ->throw();

        return new self(
            RepositoryPropertiesData::from($path)
        );
    }

    public function hasChanges(): bool
    {
        return ! empty($this->status());
    }

    public function restore(): self
    {
        Process::path($this->properties->path)
            ->run('git restore . --worktree --staged')
            ->throw();

        return $this;
    }

    public function fetch(): self
    {
        Process::path($this->properties->path)
            ->run("git fetch {$this->properties->remote} --tags")
            ->throw();

        return $this;
    }

    public function pull(): self
    {
        Process::path($this->properties->path)
            ->run(
                $this->buildRemoteCommand('git pull --rebase')
            )
            ->throw();

        return $this;
    }

    public function push(): self
    {
        Process::path($this->properties->path)
            ->run(
                $this->buildRemoteCommand('git push')
            )
            ->throw();

        return $this;
    }

    public function delete(): bool
    {
        if ($this->properties->provider
            ->repository($this->properties)
            ->delete()) {

            return ! File::isDirectory($this->properties->path)
                || File::deleteDirectory($this->properties->path);
        }

        return false;
    }

    private function status(): string
    {
        Process::path($this->properties->path)
            ->run('git update-index -q --refresh')
            ->throw();

        return Process::path($this->properties->path)
            ->run('git status --porcelain')
            ->output();
    }

    public function commit(): GitCommit
    {
        return (new GitCommit)->bind($this);
    }

    public function release(): GitRelease
    {
        return (new GitRelease)->bind($this);
    }

    private function buildRemoteCommand(string $command): string
    {
        return Arr::join(
            [$command, $this->properties->remote, $this->properties->branch],
            ' '
        );
    }
}

<?php

namespace IBroStudio\Git;

use IBroStudio\Git\Contracts\ProviderContract;
use IBroStudio\Git\DtO\RepositoryData;
use IBroStudio\Git\Exceptions\GitException;
use IBroStudio\Git\Providers\GithubProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Str;

class Repository
{
    public RepositoryData $properties;

    public function __construct(private string $path)
    {
        $this->path = realpath($this->path);

        if (! File::isDirectory($this->path . '/.git')){
            throw new GitException("Git repository not found in {$this->path}");
        }

        $this->setProperties();
    }

    public function commits()
    {
        return Commit::getInstance($this);
    }

    public function releases()
    {
        return Release::getInstance($this);
    }

    public function remote(): ProviderContract
    {
        return $this
            ->properties
            ->provider;
    }

    public function tag()
    {
        return Tag::getInstance($this);
    }

    public function hasChanges(): bool
    {
        Process::path($this->path)
            ->quietly()
            ->run('git update-index -q --refresh')
            ->throw();

        $process = Process::path($this->path)
            ->run(config('git status --porcelain'))
            ->throw();

        return ! empty($process->output());
    }

    public function setProperties()
    {
        $config = Process::path($this->path ?? config('release-manager.default.git.repository-path'))
            ->run('git config --local -l')
            ->throw();

        $origin = collect(explode("\n", $config->output()))
            ->filter(function (string $line) {
                return Str::contains($line, 'remote.origin.url');
            })
            ->first();

        preg_match('/:\/\/(?<provider>.*)\..*\/(?<username>.*)\/(?<repository>.*)\.git/', $origin, $matches);

        $this->properties = RepositoryData::from(
            match($matches['provider']) {
                'github' => GithubProvider::class,
                default => throw new \Exception('Unsupported'),
            },
            $matches['username'],
            $matches['repository'],
            $this->path
        );
    }
}

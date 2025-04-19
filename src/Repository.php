<?php

namespace IBroStudio\Git;

use IBroStudio\DataRepository\ValueObjects\GitSshUrl;
use IBroStudio\Git\Data\RepositoryData;
use IBroStudio\Git\Enums\GitProvidersEnum;
use IBroStudio\Git\Exceptions\InvalidGitRepositoryPathException;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class Repository
{
    final public function __construct(
        public RepositoryData $properties
    ) {}

    public static function open(string $path): self
    {
        if (! $realpath = realpath($path)) {
            throw new InvalidGitRepositoryPathException($path);
        }

        return new self(
            RepositoryData::from($realpath)
        );
    }

    public static function clone(
        GitSshUrl|string $url,
        string $localParentDirectoryPath,
        ?string $localRepositoryDirectoryName = null): self
    {
        if (! $realpath = realpath($localParentDirectoryPath)) {
            throw new InvalidGitRepositoryPathException($localParentDirectoryPath);
        }

        if (is_string($url)) {
            $url = GitSshUrl::from($url);
        }

        $command = Str::of('git clone ' . $url->value)
            ->when($localRepositoryDirectoryName, function (Stringable $string) use ($localRepositoryDirectoryName) {
                return $string->append(' ' . $localRepositoryDirectoryName);
            })
            ->value();

        Process::path($realpath)
            ->run($command)
            ->throw();

        return new self(
            RepositoryData::from($realpath.'/'.$url->repository)
        );
    }

    public static function fetchAll(GitProvidersEnum $provider): LazyCollection
    {
        return NewGitProvider::use($provider)->repositories()->all();
    }
}

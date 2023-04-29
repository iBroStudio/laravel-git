<?php

namespace IBroStudio\Git\DtO;

use IBroStudio\Git\Contracts\ProviderContract;
use IBroStudio\Git\Provider;
use IBroStudio\Git\Providers\Github\GithubRepository;
use IBroStudio\Git\Providers\GithubProvider;
use Spatie\LaravelData\Data;

class RepositoryData extends Data
{
    public function __construct(
        public string $name,
        public string $owner,
        public string $branch,
        public string $path,
        public ProviderContract $provider,
    ) {
    }

    public static function fromProvider(
        string $provider,
        string $username,
        string $repository,
        string $path): self
    {
        return Provider::use($provider)
            ->repository()
            ->get(
                username: $username,
                repository: $repository,
                path: $path
            );
    }

    public static function fromGithub(GithubProvider $provider, array $data, string $path): self
    {
        return new self(
            name: $data['name'],
            owner: $data['owner']['login'],
            branch: $data['default_branch'],
            path: $path,
            provider: $provider
        );
    }
}

<?php

namespace IBroStudio\Git\Data\RepositoryData\ConfigData;

use IBroStudio\DataRepository\ValueObjects\GitSshUrl;
use IBroStudio\Git\Integrations\Github\GithubResponse;
use Illuminate\Support\Arr;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class BranchData extends Data
{
    public function __construct(
        public string $name,
        public string|Optional $remote,
        public string|Optional $merge,
    ) {}

    public static function fromConfig(array $config): self
    {
        $name = key($config);

        return new self(
            name: key($config),
            remote: $config[$name]['remote'],
            merge: $config[$name]['merge'],
        );
    }

    public static function fromGithub(GithubResponse $response, ?array $data = null): self
    {
        $data = $data ?? $response->json();

        return new self(
            name: Arr::get($data, 'default_branch'),
            remote: Optional::create(),
            merge: Optional::create(),
        );
    }
}

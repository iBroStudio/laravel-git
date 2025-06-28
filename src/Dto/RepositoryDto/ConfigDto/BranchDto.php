<?php

declare(strict_types=1);

namespace IBroStudio\Git\Dto\RepositoryDto\ConfigDto;

use IBroStudio\Git\Dto\RepositoryDto\RepositoryLocalPropertiesDto;
use IBroStudio\Git\Integrations\Github\GithubResponse;
use Illuminate\Support\Arr;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class BranchDto extends Data
{
    public function __construct(
        public string $name,
        public string|Optional $remote,
        public string|Optional $merge,
    ) {}

    public static function fromLocalProperties(RepositoryLocalPropertiesDto $properties): self
    {
        $name = key($properties->branch);

        return self::from([
            'name' => $name,
            'remote' => $properties->branch[$name]['remote'],
            'merge' => $properties->branch[$name]['merge'],
        ]);
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

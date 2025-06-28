<?php

declare(strict_types=1);

namespace IBroStudio\Git\Concerns;

use IBroStudio\DataObjects\Enums\GitProvidersEnum;
use IBroStudio\Git\Dto\OwnerDto\AuthOwnerDto;
use IBroStudio\Git\Dto\OwnerDto\OrganizationOwnerDto;
use IBroStudio\Git\Dto\OwnerDto\UserOwnerDto;
use IBroStudio\Git\Dto\RepositoryDto\ConfigDto\BranchDto;
use IBroStudio\Git\Dto\RepositoryDto\ConfigDto\RemoteDto;
use IBroStudio\Git\Dto\RepositoryDto\RepositoryConfigDto;
use IBroStudio\Git\Enums\GitRepositoryVisibilitiesEnum;
use IBroStudio\Git\Integrations\Github\GithubResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\LaravelData\Optional;

trait RepositoryInstantiators
{
    public static function fromPath(string $path): self
    {
        $config = RepositoryConfigDto::from($path);

        return new self(
            name: $config->remote->url->repository,
            branch: $config->branch,
            provider: $config->remote->url->provider,
            remote: $config->remote,
            owner: UserOwnerDto::from(['name' => $config->remote->url->username]),
            localParentDirectory: Str::beforeLast($path, '/'),
            visibility: Optional::create(),
            new: Optional::create(),
            commit: Optional::create(),
        );
    }

    public static function fromGithub(GithubResponse $response): self
    {
        $data = $response->json();

        return new self(
            name: $data['name'],
            branch: BranchDto::from($response),
            provider: GitProvidersEnum::GITHUB,
            remote: RemoteDto::from($response),
            owner: match (true) {
                Arr::exists($data, 'type') && $data['type'] === 'Organization' => OrganizationOwnerDto::from(['name' => $data['owner']['login']]),
                $data['owner']['login'] === config('git.auth.github.username') => AuthOwnerDto::from(['name' => $data['owner']['login']]),
                default => UserOwnerDto::from(['name' => $data['owner']['login']]),
            },
            localParentDirectory: Optional::create(),
            visibility: GitRepositoryVisibilitiesEnum::from($data['visibility']),
            new: Optional::create(),
            commit: Optional::create(),
        );
    }

    public static function collectFromGithub(GithubResponse $response): array
    {
        return Arr::map($response->json(), function (array $data) use ($response) {
            return self::from([
                'name' => $data['name'],
                'branch' => BranchDto::from($response, $data),
                'provider' => GitProvidersEnum::GITHUB,
                'remote' => RemoteDto::from($response, $data),
                'owner' => $data['owner']['login'],
                'localParentDirectory' => Optional::create(),
                'visibility' => GitRepositoryVisibilitiesEnum::from($data['visibility']),
            ]);
        });
    }
}

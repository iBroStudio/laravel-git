<?php

use IBroStudio\Git\Contracts\GitProviderContract;
use IBroStudio\Git\Contracts\GitProviderRepositoryContract;
use IBroStudio\Git\Contracts\GitProviderUserContract;
use IBroStudio\Git\Data\RepositoryData;
use IBroStudio\Git\Enums\GitProvidersEnum;
use IBroStudio\Git\Enums\GitRepositoryVisibilitiesEnum;
use IBroStudio\Git\GitProviders\Github\GithubProvider;
use IBroStudio\Git\GitProviders\Github\GithubRepository;
use IBroStudio\Git\GitProviders\Github\GithubUser;
use IBroStudio\Git\GitProviders\GitProviderRepository;
use IBroStudio\Git\GitProviders\GitProviderUser;

it('can instantiate the provider', function () {
    $provider = app(GithubProvider::class);

    expect($provider)->toBeInstanceOf(GithubProvider::class)
        ->and(
            in_array(GitProviderContract::class, class_implements($provider))
        )->toBeTrue();

});

it('gives access to provider user', function () {
    $provider = app(GithubProvider::class);

    expect($provider->user())->toBeInstanceOf(GithubUser::class)
        ->and(
            in_array(GitProviderUser::class, class_parents($provider->user()))
        )->toBeTrue()
        ->and(
            in_array(GitProviderUserContract::class, class_implements($provider->user()))
        )->toBeTrue();
});

it('gives access to provider repository', function () {
    $provider = app(GithubProvider::class);
    $repository = $provider->repository(
        properties: new RepositoryData(
            name: fake()->slug(2),
            remote: config('git.default.remote'),
            branch: config('git.default.branch'),
            provider: GitProvidersEnum::GITHUB,
            owner: config('git.testing.github_username'),
            localParentDirectory: config('git.testing.directory'),
            visibility: GitRepositoryVisibilitiesEnum::PRIVATE,
        )
    );

    expect($repository)->toBeInstanceOf(GithubRepository::class)
        ->and(
            in_array(GitProviderRepository::class, class_parents($repository))
        )->toBeTrue()
        ->and(
            in_array(GitProviderRepositoryContract::class, class_implements($repository))
        )->toBeTrue();
});

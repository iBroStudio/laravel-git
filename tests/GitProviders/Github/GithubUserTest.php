<?php

use IBroStudio\Git\Data\GitUserInfosData;
use IBroStudio\Git\Data\GitUserOrganizationData;
use IBroStudio\Git\Data\RepositoryData;
use IBroStudio\Git\GitProviders\Github\GithubProvider;
use IBroStudio\Git\GitProviders\Github\GithubUser;

it('can retrieve user organizations', function () {
    $provider = app(GithubProvider::class);
    $providerUser = new GithubUser($provider);
    $organizations = $providerUser->organizations();

    // @phpstan-ignore-next-line
    expect($organizations)->toBeCollection()
        ->and($organizations->first())
        ->toBeInstanceOf(GitUserOrganizationData::class);
});

it('can retrieve user infos', function () {
    $provider = app(GithubProvider::class);
    $providerUser = new GithubUser($provider);

    expect($providerUser->infos())->toBeInstanceOf(GitUserInfosData::class);
});

it('can list user repositories', function () {
    $provider = app(GithubProvider::class);
    $providerUser = new GithubUser($provider);
    $repositories = $providerUser->repositories();

    // @phpstan-ignore-next-line
    expect($repositories)->toBeCollection()
        ->and($repositories->first())
        ->toBeInstanceOf(RepositoryData::class);
});

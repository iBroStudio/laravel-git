<?php

use IBroStudio\Git\Data\GitUserInfosData;
use IBroStudio\Git\Data\GitUserOrganizationData;
use IBroStudio\Git\Data\RepositoryPropertiesData;
use IBroStudio\Git\GitUser;

it('can retrieve git user organizations', function () {
    $organizations = GitUser::organizations();

    // @phpstan-ignore-next-line
    expect($organizations)->toBeCollection()
        ->and($organizations->first())->toBeInstanceOf(GitUserOrganizationData::class);
});

it('can retrieve git user informations', function () {
    expect(GitUser::infos())->toBeInstanceOf(GitUserInfosData::class);
});

it('can list user repositories', function () {
    $repositories = GitUser::repositories();

    // @phpstan-ignore-next-line
    expect($repositories)->toBeCollection()
        ->and($repositories->first())
        ->toBeInstanceOf(RepositoryPropertiesData::class);
});

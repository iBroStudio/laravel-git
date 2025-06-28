<?php

declare(strict_types=1);

use IBroStudio\Git\Integrations\Github\GithubConnector;
use IBroStudio\Git\Integrations\Github\Resources\GithubUserResource;
use IBroStudio\Git\Integrations\Github\Resources\Repositories\GithubRepositoryResource;

it('can instantiate Github connector', function () {
    expect(
        new GithubConnector(
            username: config('git.auth.github.username'),
            token: config('git.auth.github.token'),
        )
    )->toBeInstanceOf(GithubConnector::class);
});

it('can return Github authenticated user resource', function () {
    expect(githubConnector()->auth())->toBeInstanceOf(IBroStudio\Git\Integrations\Github\Resources\GithubAuthResource::class);
});

it('can return Github organizations resource', function () {
    expect(githubConnector()->organizations())->toBeInstanceOf(IBroStudio\Git\Integrations\Github\Resources\GithubOrganizationResource::class);
});

it('can return Github user resource', function () {
    expect(githubConnector()->user('Yann-iBroStudio'))->toBeInstanceOf(GithubUserResource::class);
});
/*
it('can return Github repositories resource', function () {
    expect(githubConnector()->repositories())->toBeInstanceOf(GithubRepositoryResource::class);
});
*/

<?php

declare(strict_types=1);

use IBroStudio\Git\Dto\GitUserDto;
use IBroStudio\Git\Dto\OrganizationDto;
use IBroStudio\Git\Integrations\Github\Requests\Auth\GetGithubAuthRequest;
use IBroStudio\Git\Integrations\Github\Requests\Organizations\GetGithubOrganizationsRequest;
use IBroStudio\Git\Integrations\Github\Resources\GithubOrganizationResource;
use Illuminate\Support\LazyCollection;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

it('can return authenticated Github user', function () {
    Saloon::fake([
        GetGithubAuthRequest::class => MockResponse::fixture('github/get_authenticated_user'),
    ]);

    expect(
        githubConnector()
            ->auth()
            ->get()
    )->toBeInstanceOf(GitUserDto::class);
});

it('can return authenticated user organizations resource', function () {
    expect(
        githubConnector()
            ->auth()
            ->organizations()
    )->toBeInstanceOf(GithubOrganizationResource::class);
});

it('can fetch authenticated user organizations', function () {
    Saloon::fake([
        GetGithubOrganizationsRequest::class => MockResponse::fixture('github/get_authenticated_user_organizations'),
    ]);

    $organizations = githubConnector()
        ->auth()
        ->organizations()
        ->all();

    expect($organizations)->toBeInstanceOf(LazyCollection::class)
        ->and($organizations->first())->toBeInstanceOf(OrganizationDto::class);
});

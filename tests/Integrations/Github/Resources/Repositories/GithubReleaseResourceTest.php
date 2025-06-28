<?php

declare(strict_types=1);

use IBroStudio\DataObjects\ValueObjects\SemanticVersion;
use IBroStudio\Git\Dto\RepositoryDto;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\Releases\CreateGithubReleaseRequest;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\Releases\GetGithubLatestReleaseRequest;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\Releases\GetGithubReleaseByTagRequest;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\Releases\GetGithubReleaseRequest;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\Releases\GetGithubReleasesRequest;
use IBroStudio\Git\Integrations\Github\Resources\GithubAuthResource;
use IBroStudio\Git\Integrations\Github\Resources\GithubOrganizationResource;
use IBroStudio\Git\Integrations\Github\Resources\Repositories\GithubReleaseResource;
use Illuminate\Support\LazyCollection;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

it('can return repositories releases resource', function (GithubAuthResource|GithubOrganizationResource $resource) {
    $releasesResource = $resource
        ->repositories($this->repository)
        ->releases();

    expect($releasesResource)->toBeInstanceOf(GithubReleaseResource::class);
})->with([
    fn () => githubConnector()->auth(),
    fn () => githubConnector()->organizations('iBroStudio'),
]);

it('can return repository releases', function (GithubAuthResource|GithubOrganizationResource $resource) {
    Saloon::fake([
        GetGithubReleasesRequest::class => MockResponse::fixture('github/get_repository_releases'),
    ]);

    $releases = $resource
        ->repositories($this->repository)
        ->releases()
        ->all();

    expect($releases)->toBeInstanceOf(LazyCollection::class)
        ->and($releases->first())->toBeInstanceOf(RepositoryDto\ReleaseDto::class);
})->with([
    fn () => githubConnector()->auth(),
    fn () => githubConnector()->organizations('iBroStudio'),
]);

it('can return the latest release', function (GithubAuthResource|GithubOrganizationResource $resource) {
    Saloon::fake([
        GetGithubLatestReleaseRequest::class => MockResponse::fixture('github/get_repository_latest_release'),
    ]);

    $release = $resource
        ->repositories($this->repository)
        ->releases()
        ->latest();

    expect($release)->toBeInstanceOf(RepositoryDto\ReleaseDto::class);
})->with([
    fn () => githubConnector()->auth(),
    fn () => githubConnector()->organizations('iBroStudio'),
]);

it('can return a release', function (GithubAuthResource|GithubOrganizationResource $resource) {
    Saloon::fake([
        GetGithubReleaseRequest::class => MockResponse::fixture('github/get_repository_release'),
    ]);

    $release = $resource
        ->repositories($this->repository)
        ->releases()
        ->get(177730897);

    expect($release)->toBeInstanceOf(RepositoryDto\ReleaseDto::class);
})->with([
    fn () => githubConnector()->auth(),
    fn () => githubConnector()->organizations('iBroStudio'),
]);

it('can return a release by tag name', function (GithubAuthResource|GithubOrganizationResource $resource) {
    Saloon::fake([
        GetGithubReleaseByTagRequest::class => MockResponse::fixture('github/get_repository_release_by_tag'),
    ]);

    $release = $resource
        ->repositories($this->repository)
        ->releases()
        ->getByTag('v0.0.22');

    expect($release)->toBeInstanceOf(RepositoryDto\ReleaseDto::class);
})->with([
    fn () => githubConnector()->auth(),
    fn () => githubConnector()->organizations('iBroStudio'),
]);

it('can create a release', function (GithubAuthResource|GithubOrganizationResource $resource) {
    Saloon::fake([
        CreateGithubReleaseRequest::class => MockResponse::fixture('github/create_repository_release'),
    ]);

    $changelog = $this->repository->changelog();
    $version = SemanticVersion::from('v0.0.23');
    $previous = SemanticVersion::from('v0.0.22');
    $releaseDto = RepositoryDto\ReleaseDto::from([
        'version' => $version,
        'previous' => $previous,
        'description' => $changelog->describe($version),
    ]);

    $release = $resource
        ->repositories($this->repository)
        ->releases()
        ->create($releaseDto);

    expect($release)->toBeInstanceOf(RepositoryDto\ReleaseDto::class);
})->with([
    fn () => githubConnector()->auth(),
    fn () => githubConnector()->organizations('iBroStudio'),
]);

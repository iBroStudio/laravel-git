<?php

declare(strict_types=1);

use IBroStudio\DataObjects\Enums\GitProvidersEnum;
use IBroStudio\DataObjects\ValueObjects\GitSshUrl;
use IBroStudio\Git\Dto\OwnerDto\AuthOwnerDto;
use IBroStudio\Git\Dto\OwnerDto\OrganizationOwnerDto;
use IBroStudio\Git\Dto\RepositoryDto\ConfigDto\RemoteDto;
use IBroStudio\Git\Enums\GitRepositoryTopicsEnum;
use IBroStudio\Git\Enums\GitRepositoryVisibilitiesEnum;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\GetGithubAuthRepositoriesRequest;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\GetGithubOrganizationRepositoriesRequest;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\GetGithubUserRepositoriesRequest;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\CreateGithubAuthRepositoryRequest;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\CreateGithubOrganizationRepositoryRequest;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\CreateGithubRepositoryFromTemplateRequest;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\GetGithubRepositoryRequest;
use IBroStudio\Git\Integrations\Github\Resources\GithubAuthResource;
use IBroStudio\Git\Integrations\Github\Resources\GithubOrganizationResource;
use IBroStudio\Git\Integrations\Github\Resources\GithubUserResource;
use IBroStudio\Git\Integrations\Github\Resources\Repositories\GithubRepositoryResource;
use IBroStudio\Git\Repository;
use Illuminate\Support\LazyCollection;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

it('can return repositories resource', function (GithubAuthResource|GithubOrganizationResource|GithubUserResource $resource) {

    expect($resource->repositories())->toBeInstanceOf(GithubRepositoryResource::class);
})->with([
    fn () => githubConnector()->auth(),
    fn () => githubConnector()->organizations('iBroStudio'),
    fn () => githubConnector()->user('Yann-iBroStudio'),
]);

it('can fetch repositories', function (GithubAuthResource|GithubOrganizationResource|GithubUserResource $resource) {
    Saloon::fake([
        GetGithubAuthRepositoriesRequest::class => MockResponse::fixture('github/get_authenticated_user_repositories'),
        GetGithubOrganizationRepositoriesRequest::class => MockResponse::fixture('github/get_organization_repositories'),
        GetGithubUserRepositoriesRequest::class => MockResponse::fixture('github/get_user_repositories'),
    ]);

    $repositories = $resource->repositories()->all();

    expect($repositories)->toBeInstanceOf(LazyCollection::class)
        ->and($repositories->first())->toBeInstanceOf(Repository::class);
})->with([
    fn () => githubConnector()->auth(),
    fn () => githubConnector()->organizations('iBroStudio'),
    fn () => githubConnector()->user('Yann-iBroStudio'),
]);

it('can fetch a repository', function (GithubAuthResource|GithubOrganizationResource|GithubUserResource $resource) {
    Saloon::fake([
        GetGithubRepositoryRequest::class => MockResponse::fixture('github/get_authenticated_user_repository'),
    ]);

    $repository = $resource->repositories($this->repository)->get();

    expect($repository)->toBeInstanceOf(Repository::class);
})->with([
    fn () => githubConnector()->auth(),
    fn () => githubConnector()->organizations('iBroStudio'),
    fn () => githubConnector()->user('Yann-iBroStudio'),
]);

it('can create a repository', function (GithubAuthResource|GithubOrganizationResource $resource) {
    Saloon::fake([
        CreateGithubAuthRepositoryRequest::class => MockResponse::fixture('github/create_authenticated_user_repository'),
        CreateGithubOrganizationRepositoryRequest::class => MockResponse::fixture('github/create_organization_repository'),
    ]);

    $repository = Repository::from([
        'name' => 'new-repo',
        'branch' => config('git.default.branch'),
        'owner' => $resource instanceof GithubAuthResource ?
            AuthOwnerDto::from(['name' => config('git.auth.github.username')])
            : OrganizationOwnerDto::from(['name' => 'iBroStudio']),
        'provider' => GitProvidersEnum::GITHUB,
        'remote' => RemoteDto::from([
            'name' => config('git.default.remote'),
            'url' => GitSshUrl::build(GitProvidersEnum::GITHUB, config('git.auth.github.username'), 'new-repo'),
        ]),
        'localParentDirectory' => config('git.testing.directory'),
        'visibility' => GitRepositoryVisibilitiesEnum::PRIVATE,
    ]);

    expect(
        $resource
            ->repositories($repository)
            ->create()
    )->toBeInstanceOf(Repository::class);
})->with([
    fn () => githubConnector()->auth(),
    fn () => githubConnector()->organizations('iBroStudio'),
]);

it('can create a repository from a template', function (GithubAuthResource|GithubOrganizationResource $resource) {
    Saloon::fake([
        CreateGithubRepositoryFromTemplateRequest::class => MockResponse::fixture('github/create_authenticated_user_repository_from_template'),
    ]);

    $repository = Repository::from([
        'name' => 'new-repo',
        'branch' => config('git.default.branch'),
        'owner' => $resource instanceof GithubAuthResource ?
            AuthOwnerDto::from(['name' => config('git.auth.github.username')])
            : OrganizationOwnerDto::from(['name' => 'iBroStudio']),
        'provider' => GitProvidersEnum::GITHUB,
        'remote' => RemoteDto::from([
            'name' => config('git.default.remote'),
            'url' => GitSshUrl::build(GitProvidersEnum::GITHUB, config('git.auth.github.username'), 'new-repo'),
        ]),
        'localParentDirectory' => config('git.testing.directory'),
        'visibility' => GitRepositoryVisibilitiesEnum::PRIVATE,
        'template' => 'git@github.com:spatie/package-skeleton-laravel.git',
    ]);

    expect(
        $resource
            ->repositories($repository)
            ->create()
    )->toBeInstanceOf(Repository::class);
})->with([
    fn () => githubConnector()->auth(),
    fn () => githubConnector()->organizations('iBroStudio'),
]);

it('can fetch repositories by topics', function () {
    Saloon::fake([
        GetGithubAuthRepositoriesRequest::class => MockResponse::fixture('github/get_authenticated_user_repositories'),
    ]);

    $repositories = githubConnector()->auth()->repositories()->byTopics([GitRepositoryTopicsEnum::TEMPLATE]);

    expect($repositories)->toBeInstanceOf(LazyCollection::class)
        ->and($repositories->first())->toBeInstanceOf(Repository::class);
});

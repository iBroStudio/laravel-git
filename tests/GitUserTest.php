<?php

use IBroStudio\Git\Data\GitUserInfosData;
use IBroStudio\Git\Data\GitUserOrganizationData;
use IBroStudio\Git\Data\RepositoryData;
use IBroStudio\Git\GitUser;
use IBroStudio\Git\Integrations\Github\GithubConnector;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\GetGithubRepository;
use IBroStudio\Git\Integrations\Github\Requests\Users\GetGithubUser;
use IBroStudio\Git\Integrations\Github\Resources\GithubRepository;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

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
        ->toBeInstanceOf(RepositoryData::class);
});

it('test user saloon', function () {
    Saloon::fake([
        GetGithubUser::class => MockResponse::fixture('github/get_user'),
    ]);

    $github = new GithubConnector(
        username: config('git.testing.github.username'),
        token: config('git.testing.github.token'),
    );

    $github->users()->get('Yann-iBroStudio');
});


it('test repo saloon', function () {
    Saloon::fake([
        GetGithubRepository::class => MockResponse::fixture('github/get_repository'),
    ]);

    $github = new GithubConnector(
        username: config('git.testing.github.username'),
        token: config('git.testing.github.token'),
    );

    $github->repositories()->get('Yann-iBroStudio', config('git.testing.repository'));
});

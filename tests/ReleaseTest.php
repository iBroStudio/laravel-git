<?php

declare(strict_types=1);

use IBroStudio\DataObjects\Enums\SemanticVersionEnum;
use IBroStudio\Git\Dto\RepositoryDto\ReleaseDto;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\Releases\CreateGithubReleaseRequest;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\Releases\GetGithubLatestReleaseRequest;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\Releases\GetGithubReleasesRequest;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\LazyCollection;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

it('can return repository releases', function () {
    Saloon::fake([
        GetGithubReleasesRequest::class => MockResponse::fixture('github/get_repository_releases'),
    ]);

    $releases = $this->repository->releases()->all();

    expect($releases)->toBeInstanceOf(LazyCollection::class)
        ->and($releases->first())->toBeInstanceOf(ReleaseDto::class);
});

it('can return the latest release version', function () {
    Saloon::fake([
        GetGithubLatestReleaseRequest::class => MockResponse::fixture('github/get_repository_latest_release'),
    ]);

    expect(
        $this->repository
            ->releases()
            ->latest()
    )->toBeInstanceOf(ReleaseDto::class);
});

it('can create a release', function () {
    Process::fake([
        '*' => Process::result(
            output: 'Repository has changes',
        ),
    ]);

    Saloon::fake([
        GetGithubLatestReleaseRequest::class => MockResponse::fixture('github/get_repository_latest_release'),
        CreateGithubReleaseRequest::class => MockResponse::fixture('github/create_repository_release'),
    ]);

    expect(
        $this->repository
            ->releases()
            ->create(SemanticVersionEnum::PATCH)
    )->toBeInstanceOf(ReleaseDto::class);
});

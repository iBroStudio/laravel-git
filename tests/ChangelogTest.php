<?php

declare(strict_types=1);

use IBroStudio\DataObjects\Enums\SemanticVersionEnum;
use IBroStudio\Git\Changelog;
use IBroStudio\Git\Dto\RepositoryDto\ReleaseDto;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\Releases\GetGithubLatestReleaseRequest;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\Releases\GetGithubReleasesRequest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

it('can load and parse CHANGELOG', function () {
    $changelog = $this->repository->changelog();

    expect($changelog->content)->toBeInstanceOf(Collection::class)
        ->and($changelog->content->get(Changelog::CONTENT_HEADER_KEY))->toBeArray()
        ->and($changelog->content->get(Changelog::CONTENT_SECTIONS_KEY))->toBeInstanceOf(Collection::class);
});

it('can rebuild CHANGELOG', function () {
    Saloon::fake([
        GetGithubReleasesRequest::class => MockResponse::fixture('github/get_repository_releases'),
    ]);

    $changelog = $this->repository->changelog();

    File::delete($this->path.'/CHANGELOG.md');

    expect($changelog->rebuild())->toBeTrue()
        ->and(
            File::get($this->path.'/CHANGELOG.md')
        )->toBe(
            implode("\n", $changelog->content->flatten()->toArray())
        );
});

it('can prepend a new release in CHANGELOG', function () {
    Saloon::fake([
        GetGithubLatestReleaseRequest::class => MockResponse::fixture('github/get_repository_latest_release'),
    ]);

    $latest = $this->repository->releases()->latest();

    $releaseDto = ReleaseDto::from([
        'version' => $latest->version->increment(SemanticVersionEnum::PATCH),
        'previous' => $latest->version,
    ]);

    $changelog = $this->repository->changelog();

    expect($changelog->prepend($releaseDto))->toBeTrue()
        ->and(
            File::get($this->repository->path.'/'.config('git.changelog.file'))
        )->toContain($releaseDto->version->withoutPrefix());
});

it('can pick a release description in CHANGELOG', function () {
    $changelog = $this->repository->changelog();

    expect(
        $changelog->pick('v0.0.2')
    )->toBeInstanceOf(Collection::class)
        ->and(
            $changelog->pick('v999.999.999')
        )->toBeNull();
});

it('can return a version description', function () {
    $changelog = $this->repository->changelog();

    expect($changelog->describe('v0.0.23'))->toBe('* update gitignore ([c06b5d](https://github.com/Yann-iBroStudio/test/commitc06b5d5d483bcb39c2fbe64f1fee0a4f471c9984))');
});

<?php

use IBroStudio\DataRepository\Enums\SemanticVersionSegments;
use IBroStudio\DataRepository\ValueObjects\SemanticVersion;
use IBroStudio\Git\Changelog;
use IBroStudio\Git\Contracts\ChangelogContract;
use IBroStudio\Git\Data\GitReleaseData;
use IBroStudio\Git\GitRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

it('can load and parse CHANGELOG', function () {
    $repository = GitRepository::open(config('git.testing.repository'));
    $changelog = app(ChangelogContract::class)->bind($repository);

    expect($changelog->content)->toBeInstanceOf(Collection::class)
        ->and($changelog->content->get(Changelog::CONTENT_HEADER_KEY))->toBeArray()
        ->and($changelog->content->get(Changelog::CONTENT_SECTIONS_KEY))->toBeInstanceOf(Collection::class);
});

it('can rebuild CHANGELOG', function () {
    $repository = GitRepository::open(config('git.testing.repository'));
    $changelog = app(ChangelogContract::class)->bind($repository);

    File::delete(config('git.testing.repository').'/CHANGELOG.md');

    expect($changelog->rebuild())->toBeTrue()
        ->and(
            File::get(config('git.testing.repository').'/CHANGELOG.md')
        )->toBe(
            implode("\n", $changelog->content->flatten()->toArray())
        );
});

it('can prepend a new release in CHANGELOG', function () {
    $repository = GitRepository::open(config('git.testing.repository'));
    $version = $repository->release()->latest();
    $newVersion = $version->increment(SemanticVersionSegments::PATCH);
    $changelog = app(ChangelogContract::class)->bind($repository);

    expect(
        $changelog->prepend(
            new GitReleaseData(
                version: $newVersion,
                previous: $version
            )
        )
    )->toBeTrue()
        ->and(
            File::get($repository->properties->path.'/'.config('git.changelog.file'))
        )->toContain($newVersion->withoutPrefix()->value());
});

it('can pick a release description in CHANGELOG', function () {
    $repository = GitRepository::open(config('git.testing.repository'));
    $changelog = (new Changelog)->bind($repository);

    expect(
        $changelog->pick(SemanticVersion::make('v0.0.2'))
    )->toBeInstanceOf(Collection::class)
        ->and(
            $changelog->pick(SemanticVersion::make('v999.999.999'))
        )->toBeNull();
});

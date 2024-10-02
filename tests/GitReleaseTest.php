<?php

use IBroStudio\DataRepository\Enums\SemanticVersionSegments;
use IBroStudio\DataRepository\ValueObjects\SemanticVersion;
use IBroStudio\Git\Contracts\ChangelogContract;
use IBroStudio\Git\Data\GitReleaseData;
use IBroStudio\Git\GitRelease;
use IBroStudio\Git\GitRepository;

it('can instantiate the release class', function () {
    expect(
        GitRepository::open(config('git.testing.repository'))
            ->release()
    )->toBeInstanceOf(GitRelease::class);
});

it('can retrieve all releases', function () {
    $releases = GitRepository::open(config('git.testing.repository'))
        ->release()
        ->all();

    // @phpstan-ignore-next-line
    expect($releases)->toBeCollection()
        ->and($releases->first())->toBeInstanceOf(GitReleaseData::class);
});

it('can retrieve the latest release version', function () {
    expect(
        GitRepository::open(config('git.testing.repository'))
            ->release()
            ->latest()
    )->toBeInstanceOf(SemanticVersion::class);
});

it('can create a release', function () {
    $repository = GitRepository::open(config('git.testing.repository'));
    $version = $repository->release()->latest();
    $newVersion = $version->increment(SemanticVersionSegments::PATCH);
    $release = $repository
        ->release()
        ->create($newVersion, app(ChangelogContract::class)->bind($repository));

    expect($release)->toBeInstanceOf(SemanticVersion::class)
        ->and(
            $repository->release()->latest()->value()
        )->toBe(
            $newVersion->value()
        );
});

<?php

use IBroStudio\DataRepository\Enums\SemanticVersionSegments;
use IBroStudio\DataRepository\ValueObjects\SemanticVersion;
use IBroStudio\Git\Changelog;
use IBroStudio\Git\Data\GitReleaseData;
use IBroStudio\Git\GitRepository;

it('can retrieve all releases', function () {
    $repository = GitRepository::open(config('git.testing.repository'));
    $releases = $repository
        ->properties
        ->provider
        ->repository($repository->properties)
        ->release()
        ->all();

    // @phpstan-ignore-next-line
    expect($releases)->toBeCollection()
        ->and($releases->first())->toBeInstanceOf(GitReleaseData::class);
});

it('can retrieve the latest release version', function () {
    $repository = GitRepository::open(config('git.testing.repository'));

    expect(
        $repository
            ->properties
            ->provider
            ->repository($repository->properties)
            ->release()
            ->latest()
    )->toBeInstanceOf(SemanticVersion::class);
});

it('can create a release', function () {
    $repository = GitRepository::open(config('git.testing.repository'));
    $version = $repository->release()->latest();
    $newVersion = $version->increment(SemanticVersionSegments::PATCH);

    expect(
        $repository
            ->properties
            ->provider
            ->repository($repository->properties)
            ->release()
            ->create($newVersion, (new Changelog)->bind($repository))
    )->toBeInstanceOf(SemanticVersion::class)
        ->and(
            $repository->release()->latest()->value()
        )->toBe(
            $newVersion->value()
        );
});

<?php

use IBroStudio\DataRepository\ValueObjects\SemanticVersion;
use IBroStudio\Git\Contracts\GitProviderContract;
use IBroStudio\Git\GitRepository;

it('new can instantiate the provider class', function () {
    dd(\IBroStudio\Git\NewGitProvider::use(\IBroStudio\Git\Enums\GitProvidersEnum::GITHUB));
    expect(
        \IBroStudio\Git\NewGitProvider::use(\IBroStudio\Git\Enums\GitProvidersEnum::GITHUB)
    )->toBeInstanceOf(\IBroStudio\Git\NewGitProvider::class);
});

it('can instantiate the provider class', function () {
    expect(
        GitRepository::open(config('git.testing.repository'))
            ->properties
            ->provider
    )->toBeInstanceOf(GitProviderContract::class);
});

it('can instantiate the provider release class', function () {
    GitRepository::open(config('git.testing.repository'))
        ->release();
})->throwsNoExceptions();

it('can retrieve the latest release version', function () {
    expect(
        GitRepository::open(config('git.testing.repository'))
            ->release()
            ->latest()
    )->toBeInstanceOf(SemanticVersion::class);
});

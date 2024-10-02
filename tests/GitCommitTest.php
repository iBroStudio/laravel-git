<?php

use IBroStudio\Git\Data\GitCommitData;
use IBroStudio\Git\GitCommit;
use IBroStudio\Git\GitRepository;
use Illuminate\Support\LazyCollection;

it('can retrieve the last commit', function () {
    expect(
        GitRepository::open(config('git.testing.repository'))
            ->commit()
            ->last()
    )->toBeInstanceOf(GitCommitData::class);
});

it('can retrieve commits history', function () {
    $history = (new GitCommit)
        ->bind(GitRepository::open(config('git.testing.repository')))
        ->history();

    expect($history)->toBeInstanceOf(LazyCollection::class)
        ->and($history->first())->toBeInstanceOf(GitCommitData::class);
});

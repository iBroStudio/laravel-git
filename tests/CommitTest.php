<?php

declare(strict_types=1);

use IBroStudio\Git\Dto\RepositoryDto\CommitDto;
use Illuminate\Support\LazyCollection;

it('can retrieve the last commit', function () {
    [$repository] = openRepository();

    expect($repository->commits()->last())->toBeInstanceOf(CommitDto::class);
});

it('can retrieve commits history', function () {
    [$repository] = openRepository();
    $history = $repository->commits()->history();

    expect($history)->toBeInstanceOf(LazyCollection::class)
        ->and($history->first())->toBeInstanceOf(CommitDto::class);
});

it('can create a commit', function () {
    [$repository, $path] = openRepository();
    [$commit, $file] = commitTest($path);

    $repository->commits()->add($commit);

    expect($repository->commits()->last()->message)->toBe($commit->message);

    cleanCommitTest($repository, $file);
});

it('can undo the last commit', function () {
    [$repository, $path] = openRepository();
    [$commit, $file] = commitTest($path);
    $lastCommit = $repository->commits()->last();
    $repository->commits()->add($commit);

    expect($repository->commits()->last()->hash)->not->toBe($lastCommit->hash);

    $repository->commits()->undo();

    expect($repository->commits()->last()->hash)->toBe($lastCommit->hash);

    File::delete($file);
});

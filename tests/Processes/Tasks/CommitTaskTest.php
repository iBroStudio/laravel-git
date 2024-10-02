<?php

use IBroStudio\DataRepository\Enums\SemanticVersionSegments;
use IBroStudio\Git\Actions\CommitAction;
use IBroStudio\Git\Data\GitCommitData;
use IBroStudio\Git\Data\GitReleaseData;
use IBroStudio\Git\Enums\GitCommitTypes;
use IBroStudio\Git\GitRepository;
use IBroStudio\Git\Processes\Payloads\CreateRepositoryReleasePayload;
use Illuminate\Support\Facades\File;

it('can commit', function () {
    $repository = GitRepository::open(config('git.testing.repository'));
    $version = $repository->release()->latest();
    $newVersion = $version->increment(SemanticVersionSegments::PATCH);
    $message = 'test commit '.\Carbon\Carbon::now()->format('Y-m-d H:i:s');
    $payload = new CreateRepositoryReleasePayload(
        repository: $repository,
        releaseData: new GitReleaseData(
            version: $newVersion,
            previous: $version,
            published_at: new DateTime,
        ),
        commitData: new GitCommitData(
            type: GitCommitTypes::TEST,
            message: $message,
        )
    );

    $file = fake()->word().'.txt';
    File::put(
        path: config('git.testing.repository').'/'.$file,
        contents: 'test'
    );

    $commit = (new CommitAction)->execute(
        commitData: $payload->getCommitData(),
        repository: $repository
    );

    expect($commit)->toBeInstanceOf(GitCommitData::class)
        ->and($commit->message)->toEqual($message);

    $repository->commit()->undo();
    File::delete($file);
});

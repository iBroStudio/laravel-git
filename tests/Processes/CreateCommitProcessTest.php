<?php

use Carbon\Carbon;
use IBroStudio\Git\Data\GitCommitData;
use IBroStudio\Git\Enums\GitCommitTypes;
use IBroStudio\Git\GitRepository;
use IBroStudio\Git\Processes\CreateCommitProcess;
use IBroStudio\Git\Processes\Payloads\CreateCommitPayload;
use Illuminate\Support\Facades\File;

it('can process a commit', function () {
    $repository = GitRepository::open(config('git.testing.repository'));
    $message = 'test commit '.Carbon::now()->format('Y-m-d H:i:s');
    $payload = new CreateCommitPayload(
        repository: $repository,
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

    $process = (new CreateCommitProcess)
        ->run(
            new CreateCommitPayload(
                repository: $payload->getRepository(),
                commitData: $payload->getCommitData()
            )
        );

    $commit = $process->getCommitData();

    expect($commit)->toBeInstanceOf(GitCommitData::class)
        ->and($commit->message)->toEqual($message);

    $repository->commit()->undo();
    File::delete($file);
});

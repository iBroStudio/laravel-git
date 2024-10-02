<?php

use IBroStudio\DataRepository\Enums\SemanticVersionSegments;
use IBroStudio\Git\Data\GitReleaseData;
use IBroStudio\Git\GitRepository;
use IBroStudio\Git\Processes\CreateRepositoryReleaseProcess;
use IBroStudio\Git\Processes\Payloads\CreateRepositoryReleasePayload;

it('can process release creation', function () {
    $repository = GitRepository::open(config('git.testing.repository'));
    $repository->restore();
    $version = $repository->release()->latest();
    $newVersion = $version->increment(SemanticVersionSegments::PATCH);

    $process = (new CreateRepositoryReleaseProcess)
        ->run(
            new CreateRepositoryReleasePayload(
                repository: $repository,
                releaseData: new GitReleaseData(
                    version: $newVersion,
                    previous: $version
                )
            )
        );

    $repository = $process->getRepository();

    expect(
        $repository->release()->latest()->value()
    )->toBe(
        $newVersion->value()
    );
});

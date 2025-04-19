<?php

use IBroStudio\Git\GitRepository;
use IBroStudio\Git\Processes\Tasks\WaitForTemplateCommitTask;
use IBroStudio\Git\Data\RepositoryData;
use IBroStudio\Git\Processes\InitRemoteRepositoryProcess;
use IBroStudio\Git\Processes\Payloads\InitRemoteRepositoryPayload;

it('can wait for the first commit of a remote repository ', function () {
    $repository = GitRepository::open(config('git.testing.repository'));
    $payload = new InitRemoteRepositoryPayload($repository->properties);

    expect(
        (new WaitForTemplateCommitTask)($payload, fn () => true)
    )->toBeTrue();
});

it('throws exception if there is no commit', function () {
    $repository = GitRepository::open(config('git.testing.directory').'/no-commits');
    retry([100, 200, 300], function () use ($repository) {

        $commits = $repository
            ->properties
            ->provider
            ->api()
            ->repo()
            ->commits()
            ->all(
                $repository->properties->owner,
                $repository->properties->name,
                ['sha' => $repository->properties->branch]
            );

        if (count($commits) > 0) {
            return true;
        }

        throw new \RuntimeException;
    });
})->throws(\RuntimeException::class);

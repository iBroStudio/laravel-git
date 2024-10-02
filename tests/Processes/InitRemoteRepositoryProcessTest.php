<?php

use IBroStudio\Git\Data\RepositoryPropertiesData;
use IBroStudio\Git\Enums\GitProvidersEnum;
use IBroStudio\Git\Enums\GitRepositoryVisibilities;
use IBroStudio\Git\GitRepository;
use IBroStudio\Git\Processes\InitRemoteRepositoryProcess;
use IBroStudio\Git\Processes\Payloads\InitRemoteRepositoryPayload;
use Illuminate\Support\Facades\File;

it('can process remote repository init', function () {
    $process = (new InitRemoteRepositoryProcess)
        ->run(
            new InitRemoteRepositoryPayload(
                new RepositoryPropertiesData(
                    name: fake()->word(),
                    branch: config('git.default.branch'),
                    owner: config('git.testing.github_username'),
                    provider: GitProvidersEnum::GITHUB,
                    remote: config('git.default.remote'),
                    localParentDirectory: config('git.testing.directory'),
                    visibility: GitRepositoryVisibilities::PRIVATE,
                )
            )
        );

    $repository = $process->getRepository();

    expect($repository)->toBeInstanceOf(GitRepository::class)
        ->and($repository->properties->path)->toBeReadableDirectory();

    $repository
        ->properties
        ->provider
        ->repository($repository->properties)
        ->delete();

    File::deleteDirectory($repository->properties->path);
});

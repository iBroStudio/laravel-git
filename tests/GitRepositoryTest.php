<?php

use IBroStudio\DataRepository\ValueObjects\GitSshUrl;
use IBroStudio\Git\Data\RepositoryData;
use IBroStudio\Git\Enums\GitProvidersEnum;
use IBroStudio\Git\Enums\GitRepositoryVisibilitiesEnum;
use IBroStudio\Git\GitCommit;
use IBroStudio\Git\GitRepository;
use IBroStudio\Git\Processes\InitRemoteRepositoryProcess;
use IBroStudio\Git\Processes\Payloads\InitRemoteRepositoryPayload;
use IBroStudio\Git\Repository;
use Illuminate\Support\Facades\File;
use Illuminate\Support\LazyCollection;

it('new can open a repository', function () {
    expect(
        Repository::open(
            config('git.testing.directory') . '/' . config('git.testing.repository')
        )
    )->toBeInstanceOf(Repository::class);
});

it('new can clone a repository', function () {
    $directory = config('git.testing.directory');
    $url = config('git.testing.clonable');
    $gitUrl = GitSshUrl::from($url);

    File::deleteDirectory($path = $directory.'/'.$gitUrl->repository);

    expect($path)->not()->toBeDirectory()
        ->and(
            Repository::clone(
                url: $url,
                localParentDirectoryPath: $directory,
            )
        )->toBeInstanceOf(Repository::class)
        ->and($path)->toBeDirectory();
});

it('new can return repositories', function () {
    $repositories = Repository::fetchAll(GitProvidersEnum::GITHUB);

    expect($repositories)->toBeInstanceOf(LazyCollection::class);
    expect($repositories->first())->toBeInstanceOf(RepositoryData::class);
});

it('new can init a repository', function () {
    $propertiesData = new RepositoryData(
        name: fake()->word(),
        branch: config('git.default.branch'),
        owner: config('git.testing.github_username'),
        provider: GitProvidersEnum::GITHUB,
        remote: config('git.default.remote'),
        localParentDirectory: config('git.testing.directory'),
        visibility: GitRepositoryVisibilitiesEnum::PRIVATE,
    );

    $payload = Mockery::mock(InitRemoteRepositoryPayload::class);
    $payload
        ->shouldReceive('getRepository')
        ->andReturn(
            new GitRepository($propertiesData)
        );

    $process = Mockery::mock(InitRemoteRepositoryProcess::class);
    $process->shouldReceive('run')->andReturn($payload);

    $repository = GitRepository::init(
        process: $process,
        payload: $payload
    );

    expect($repository)->toBeInstanceOf(GitRepository::class);
});

it('can open a repository', function () {
    expect(
        GitRepository::open(config('git.testing.repository'))
    )->toBeInstanceOf(GitRepository::class);
});

it('can clone a repository', function () {
    $clonable = GitSshUrl::make(config('git.testing.clonable'));
    $path = config('git.testing.directory').'/'.$clonable->repository();

    if (File::isDirectory($path)) {
        File::deleteDirectory($path);
    }

    expect(
        GitRepository::clone(
            ssh_url: $clonable,
            localParentDirectory: config('git.testing.directory'),
        )
    )->toBeInstanceOf(GitRepository::class);
});

it('can init a repository', function () {
    $propertiesData = new RepositoryData(
        name: fake()->word(),
        branch: config('git.default.branch'),
        owner: config('git.testing.github_username'),
        provider: GitProvidersEnum::GITHUB,
        remote: config('git.default.remote'),
        localParentDirectory: config('git.testing.directory'),
        visibility: GitRepositoryVisibilitiesEnum::PRIVATE,
    );

    $payload = Mockery::mock(InitRemoteRepositoryPayload::class);
    $payload
        ->shouldReceive('getRepository')
        ->andReturn(
            new GitRepository($propertiesData)
        );

    $process = Mockery::mock(InitRemoteRepositoryProcess::class);
    $process->shouldReceive('run')->andReturn($payload);

    $repository = GitRepository::init(
        process: $process,
        payload: $payload
    );

    expect($repository)->toBeInstanceOf(GitRepository::class);
});

it('can check if a repository has changes', function () {
    expect(
        GitRepository::open(config('git.testing.repository'))
            ->hasChanges()
    )->toBeBool();
});

it('can restore a repository', function () {
    $repository = GitRepository::open(config('git.testing.repository'));
    $origin = File::get(config('git.testing.repository').'/README.md');

    File::put(
        path: config('git.testing.repository').'/README.md',
        contents: ''
    );

    expect($repository->hasChanges())->toBeTrue()
        ->and(
            File::get(config('git.testing.repository').'/README.md')
        )->toEqual('')
        ->and($repository->restore())->toBeInstanceOf(GitRepository::class)
        ->and(
            File::get(config('git.testing.repository').'/README.md')
        )->toEqual($origin);

});

it('can instantiate the commit class', function () {
    expect(
        GitRepository::open(config('git.testing.repository'))
            ->commit()
    )
        ->toBeInstanceOf(GitCommit::class);
});

it('can fetch remote branches and tags', function () {
    GitRepository::open(config('git.testing.repository'))
        ->fetch();
})->throwsNoExceptions();

it('can pull from remote repository', function () {
    GitRepository::open(config('git.testing.repository'))
        ->restore()
        ->pull();
})->throwsNoExceptions();

it('can push to remote repository', function () {
    GitRepository::open(config('git.testing.repository'))
        ->push();
})->throwsNoExceptions();

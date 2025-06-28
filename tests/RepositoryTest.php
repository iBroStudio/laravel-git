<?php

declare(strict_types=1);

use IBroStudio\DataObjects\Enums\GitProvidersEnum;
use IBroStudio\DataObjects\ValueObjects\GitSshUrl;
use IBroStudio\Git\Dto\OwnerDto\AuthOwnerDto;
use IBroStudio\Git\Dto\RepositoryDto\ConfigDto\RemoteDto;
use IBroStudio\Git\Enums\GitRepositoryVisibilitiesEnum;
use IBroStudio\Git\Integrations\Github\Requests\Repositories\Repository\CreateGithubAuthRepositoryRequest;
use IBroStudio\Git\Repository;
use Illuminate\Support\Facades\File;
use Saloon\Http\Faking\MockResponse;
use Saloon\Laravel\Facades\Saloon;

it('can open a repository', function () {
    expect($this->repository)->toBeInstanceOf(Repository::class);
});

it('can clone a repository', function () {
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

it('can init a repository', function () {
    Saloon::fake([
        CreateGithubAuthRepositoryRequest::class => MockResponse::fixture('github/create_authenticated_user_repository'),
    ]);

    expect(
        // *
        Repository::init([
            'name' => 'new-repo',
            'localParentDirectory' => config('git.testing.directory'),
        ])
        // */
        /*
        Repository::init([
            'name' => 'new-repo',
            'branch' => config('git.default.branch'),
            'owner' => AuthOwnerDto::from(['name' => config('git.auth.github.username')]),
            'provider' => GitProvidersEnum::GITHUB,
            'remote' => RemoteDto::from([
                'name' => config('git.default.remote'),
                'url' => GitSshUrl::build(GitProvidersEnum::GITHUB, config('git.auth.github.username'), 'new-repo'),
            ]),
            'localParentDirectory' => config('git.testing.directory'),
            'visibility' => GitRepositoryVisibilitiesEnum::PRIVATE,
        ])
        //*/
    )->toBeInstanceOf(Repository::class);
});

it('can fetch remote branches and tags', function () {
    $this->repository->fetch();
})->throwsNoExceptions();

it('can pull from remote', function () {
    $this->repository->restore();
    $this->repository->pull();
})->throwsNoExceptions();

it('can push to remote', function () {
    $this->repository->push();
})->throwsNoExceptions();

it('can return repository changes', function () {
    expect($this->repository->status())->toBeString();
});

it('can check if a repository has changes', function () {
    expect($this->repository->hasChanges())->toBeBool();
});

it('can restore a repository', function () {
    $origin = File::get($this->path.'/README.md');

    File::put(
        path: $this->path.'/README.md',
        contents: ''
    );

    expect($this->repository->hasChanges())->toBeTrue()
        ->and(
            File::get($this->path.'/README.md')
        )->toEqual('')
        ->and($this->repository->restore())->toBeTrue()
        ->and(
            File::get($this->path.'/README.md')
        )->toEqual($origin);
});

it('can return repository config', function () {
    expect($this->repository->config())->toBeArray();
});

it('can return repository config item', function () {
    expect($this->repository->config('test'))->toBe('ok');
});

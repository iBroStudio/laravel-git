<?php

use IBroStudio\DataRepository\ValueObjects\GitSshUrl;
use IBroStudio\Git\Contracts\GitProviderRepositoryContract;
use IBroStudio\Git\Data\RepositoryData;
use IBroStudio\Git\Enums\GitProvidersEnum;
use IBroStudio\Git\Enums\GitRepositoryVisibilitiesEnum;
use IBroStudio\Git\Exceptions\GitRepositoryMissingPropertyException;
use IBroStudio\Git\GitProviders\Github\GithubRepository;
use IBroStudio\Git\GitRepository;
use Illuminate\Support\Facades\File;

it('can init a repository', function () {
    clearGithubRepository();
    $repository = (new GithubRepository(
        properties: new RepositoryData(
            name: 'test-github-repository',
            branch: config('git.default.branch'),
            owner: config('git.testing.github_username'),
            provider: GitProvidersEnum::GITHUB,
            remote: config('git.default.remote'),
            localParentDirectory: config('git.testing.directory'),
            visibility: GitRepositoryVisibilitiesEnum::PRIVATE,
        )
    ))->init();

    expect($repository)->toBeInstanceOf(GitProviderRepositoryContract::class);
});

it('can clone a repository', function () {
    $cloneable = GitSshUrl::make('git@github.com:Yann-iBroStudio/test-github-repository.git');
    $path = config('git.testing.directory').'/'.$cloneable->repository();

    if (File::isDirectory($path)) {
        File::deleteDirectory($path);
    }

    expect(
        GitRepository::clone(
            ssh_url: $cloneable,
            localParentDirectory: config('git.testing.directory'),
        )
    )->toBeInstanceOf(GitRepository::class);
})
    ->depends('it can init a repository');

it('can update the repository visibility', function () {
    $repository = GitRepository::open(config('git.testing.directory').'/test-github-repository');
    $current = $repository->properties->visibility;
    $new = $current === GitRepositoryVisibilitiesEnum::PUBLIC ?
        GitRepositoryVisibilitiesEnum::PRIVATE : GitRepositoryVisibilitiesEnum::PUBLIC;

    $repository
        ->properties
        ->provider
        ->repository($repository->properties)
        ->visibility($new);

    expect($repository->properties->refresh()->visibility)
        ->toBe($new);
})
    ->depends('it can init a repository', 'it can clone a repository');

it('can delete a repository', function () {
    $repository = GitRepository::open(config('git.testing.directory').'/test-github-repository');

    expect(
        $repository
            ->properties
            ->provider
            ->repository($repository->properties)
            ->delete()
    )->toBeTrue();

    File::deleteDirectory($repository->properties->path);
})
    ->depends(
        'it can init a repository',
        'it can clone a repository',
        'it can update the repository visibility'
    );

it('can init a repository from a template', function () {
    clearGithubTemplateRepository();
    $template = GitSshUrl::make(config('git.templates')[0]);
    $repository = (new GithubRepository(
        properties: new RepositoryData(
            name: 'test-github-repository-with-template',
            branch: config('git.default.branch'),
            owner: config('git.testing.github_username'),
            provider: GitProvidersEnum::GITHUB,
            remote: config('git.default.remote'),
            localParentDirectory: config('git.testing.directory'),
            visibility: GitRepositoryVisibilitiesEnum::PRIVATE,
            templateOwner: $template->username(),
            templateRepo: $template->repository(),
        )
    ))->initFromTemplate();

    expect($repository)->toBeInstanceOf(GitProviderRepositoryContract::class);
});

it('can not init a repository from a template if templateOwner is missing', function () {
    $template = GitSshUrl::make(config('git.templates')[0]);
    (new GithubRepository(
        properties: new RepositoryData(
            name: 'test-github-repository-with-template',
            branch: config('git.default.branch'),
            owner: config('git.testing.github_username'),
            provider: GitProvidersEnum::GITHUB,
            remote: config('git.default.remote'),
            localParentDirectory: config('git.testing.directory'),
            visibility: GitRepositoryVisibilitiesEnum::PRIVATE,
            templateRepo: $template->repository(),
        )
    ))->initFromTemplate();
})
    ->throws(
        GitRepositoryMissingPropertyException::class,
        'Template owner is missing'
    );

it('can not init a repository from a template if templateRepo is missing', function () {
    $template = GitSshUrl::make(config('git.templates')[0]);
    (new GithubRepository(
        properties: new RepositoryData(
            name: 'test-github-repository-with-template',
            branch: config('git.default.branch'),
            owner: config('git.testing.github_username'),
            provider: GitProvidersEnum::GITHUB,
            remote: config('git.default.remote'),
            localParentDirectory: config('git.testing.directory'),
            visibility: GitRepositoryVisibilitiesEnum::PRIVATE,
            templateOwner: $template->username(),
        )
    ))->initFromTemplate();
})
    ->throws(
        GitRepositoryMissingPropertyException::class,
        'Template repository is missing'
    );

it('can delete a repository built from a template', function () {
    expect(
        (new GithubRepository(
            properties: new RepositoryData(
                name: 'test-github-repository-with-template',
                branch: config('git.default.branch'),
                owner: config('git.testing.github_username'),
                provider: GitProvidersEnum::GITHUB,
                remote: config('git.default.remote'),
                localParentDirectory: config('git.testing.directory'),
                visibility: GitRepositoryVisibilitiesEnum::PRIVATE,
            )
        ))->delete()
    )->toBeTrue();
})
    ->depends('it can init a repository from a template');

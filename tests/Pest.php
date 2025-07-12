<?php

declare(strict_types=1);

use Github\Exception\RuntimeException;
use GrahamCampbell\GitHub\GitHubManager;
use IBroStudio\Git\Dto\RepositoryDto\CommitDto;
use IBroStudio\Git\Enums\CommitTypeEnum;
use IBroStudio\Git\Integrations\Github\GithubConnector;
use IBroStudio\Git\Repository;
use IBroStudio\Git\Tests\TestCase;
use Saloon\Config;

// Config::preventStrayRequests();

pest()->printer()->compact();

pest()
    ->extends(TestCase::class)
    ->in(__DIR__);

function openRepository(): array
{
    $path = config('git.testing.directory').'/'.config('git.testing.repository');

    return [Repository::open($path), $path];
}

function commitTest(string $path): array
{
    $file = fake()->word().'.txt';
    File::put(
        path: $path.'/'.$file,
        contents: 'test'
    );

    return [
        CommitDto::from(CommitTypeEnum::TEST, 'test commit '.Carbon\CarbonImmutable::now()->format('Y-m-d H:i:s')),
        $file,
    ];
}

function cleanCommitTest(Repository $repository, string $file): void
{
    $repository->commits()->undo();
    File::delete($file);
}

function githubConnector(): GithubConnector
{
    return new GithubConnector(
        username: config('git.auth.github.username'),
        token: config('git.auth.github.token'),
    );
}

function clearGithubRepository()
{
    $github = app(GitHubManager::class);

    try {
        $github->repo()->show(
            config('git.auth.github_username'),
            'test-github-repository'
        );
        $github->repo()->remove(
            config('git.auth.github_username'),
            'test-github-repository'
        );
    } catch (RuntimeException $e) {
    }

    try {
        $github->repo()->show(
            config('git.auth.github_username'),
            'test-github-repository-with-template'
        );
        $github->repo()->remove(
            config('git.auth.github_username'),
            'test-github-repository-with-template'
        );
    } catch (RuntimeException $e) {
    }
}

function clearGithubTemplateRepository()
{
    $github = app(GitHubManager::class);

    try {
        $github->repo()->show(
            config('git.auth.github_username'),
            'test-github-repository'
        );
        $github->repo()->remove(
            config('git.auth.github_username'),
            'test-github-repository'
        );
    } catch (RuntimeException $e) {
    }

    try {
        $github->repo()->show(
            config('git.auth.github_username'),
            'test-github-repository-with-template'
        );
        $github->repo()->remove(
            config('git.auth.github_username'),
            'test-github-repository-with-template'
        );
    } catch (RuntimeException $e) {
    }
}

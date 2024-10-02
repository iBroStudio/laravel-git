<?php

use Github\Exception\RuntimeException;
use GrahamCampbell\GitHub\GitHubManager;
use IBroStudio\Git\Tests\TestCase;

pest()->printer()->compact();

pest()
    ->extends(TestCase::class)
    ->in(__DIR__);

function clearGithubRepository()
{
    $github = app(GitHubManager::class);

    try {
        $github->repo()->show(
            config('git.testing.github_username'),
            'test-github-repository'
        );
        $github->repo()->remove(
            config('git.testing.github_username'),
            'test-github-repository'
        );
    } catch (RuntimeException $e) {
    }

    try {
        $github->repo()->show(
            config('git.testing.github_username'),
            'test-github-repository-with-template'
        );
        $github->repo()->remove(
            config('git.testing.github_username'),
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
            config('git.testing.github_username'),
            'test-github-repository'
        );
        $github->repo()->remove(
            config('git.testing.github_username'),
            'test-github-repository'
        );
    } catch (RuntimeException $e) {
    }

    try {
        $github->repo()->show(
            config('git.testing.github_username'),
            'test-github-repository-with-template'
        );
        $github->repo()->remove(
            config('git.testing.github_username'),
            'test-github-repository-with-template'
        );
    } catch (RuntimeException $e) {
    }
}
